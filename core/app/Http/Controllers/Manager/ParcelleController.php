<?php

namespace App\Http\Controllers\Manager;

use SimpleXMLElement;
use App\Models\Section;
use App\Models\Localite;
use App\Models\Parcelle;
use App\Constants\Status;
use App\Models\Programme;
use App\Models\Producteur;
use App\Models\Cooperative;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Certification;
use App\Imports\ParcelleImport;
use App\Models\VarieteParcelle;
use App\Exports\ExportParcelles;
use App\Models\Agroespecesarbre;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\agroespeceabre_parcelle;
use App\Models\AutreAgroespecesarbreParcelle;
use App\Models\Parcelle_type_protection;
use App\Models\Producteur_certification;

class ParcelleController extends Controller
{

    public function index()
    {
        $pageTitle      = "Gestion des parcelles";
        $manager   = auth()->user();
        // $localites = Localite::joinRelationship('section')->where([['cooperative_id',$manager->cooperative_id],['localites.status',1]])->get();
        $cooperative = Cooperative::with('sections.localites')->find($manager->cooperative_id);
        $sections = Section::where('cooperative_id', $manager->cooperative_id)->get();
        $localites = Localite::joinRelationship('section')
            ->where('cooperative_id', $manager->cooperative_id)
            ->when(request()->section, function ($query, $section) {
                $query->where('section_id', $section);
            })
            ->get();
        $producteurs = Producteur::joinRelationship('localite.section')->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])
            ->where('cooperative_id', $manager->cooperative_id)
            ->when(request()->localite, function ($query, $localite) {
                $query->where('localite_id', $localite);
            })
            ->get();

        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $producteurs = Producteur::joinRelationship('localite.section')->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->get();

        $parcelles = Parcelle::dateFilter()->searchable(['codeParc'])
            ->latest('id')
            ->joinRelationship('producteur.localite.section')
            ->where('cooperative_id', $manager->cooperative_id)
            ->when(request()->section, function ($query, $section) {
                $query->where('localites.section_id', $section);
            })
            ->when(request()->localite, function ($query, $localite) {
                $query->where('producteurs.localite_id', $localite);
            })
            ->when(request()->producteur, function ($query, $producteur) {
                $query->where('producteur_id', $producteur);
            })
            ->when(request()->typedeclaration, function ($query, $typedeclaration) {
                $query->where('typedeclaration', $typedeclaration);
            })
            ->with(['producteur.localite.section']); // Charger les relations nécessaires
        $parcellesFiltre = $parcelles->get();
        $parcelles = $parcelles->paginate(getPaginate());
        $total_parcelle = $parcellesFiltre->count();
        $total_parcelle_gps = $parcellesFiltre->where('typedeclaration', 'GPS')->count();
        $total_parcelle_verbale = $parcellesFiltre->where('typedeclaration', 'Verbale')->count();

        return view('manager.parcelle.index', compact('pageTitle', 'sections', 'parcelles', 'localites', 'producteurs', 'total_parcelle', 'total_parcelle_gps', 'total_parcelle_verbale'));
    }

    public function mapping(Request $request)
    {
        $pageTitle      = "Gestion de mapping des parcelles";
        $manager   = auth()->user();

        $cooperative = Cooperative::with('sections.localites')->find($manager->cooperative_id);

        $sections = Section::where('cooperative_id', $manager->cooperative_id)->get();

        $localites = Localite::joinRelationship('section')
            ->where('cooperative_id', $manager->cooperative_id)
            ->when(request()->section, function ($query, $section) {
                $query->where('section_id', $section);
            })
            ->get();
        $producteurs = Producteur::joinRelationship('localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])
            ->when(request()->localite, function ($query, $localite) {
                $query->where('localite_id', $localite);
            })
            ->get();
        $parcelles = Parcelle::dateFilter()->latest('id')
            ->joinRelationship('producteur.localite.section')
            ->where('cooperative_id', $manager->cooperative_id)
            ->when(request()->section, function ($query, $section) {
                $query->where('section_id', $section);
            })
            ->when(request()->localite, function ($query, $localite) {
                $query->where('localite_id', $localite);
            })
            ->when(request()->producteur, function ($query, $producteur) {
                $query->where('producteur_id', $producteur);
            })
            ->with(['producteur.localite.section']) // Charger les relations nécessaires
            ->get();

                        // Téléchargement du fichier KML

                        if($request->has('download')) {
                            $datakml = '';
                            if ($request->download == 'kml') {
                                if (count($parcelles)>=1) {
                                    $kml_file_path =  base_path('mappingparcelle.kml');
                                    if (file_exists($kml_file_path)) {
                                        unlink($kml_file_path);
                                    }

                                    $datakml .= '<?xml version="1.0" encoding="utf-8" ?>
            <kml xmlns="http://www.opengis.net/kml/2.2">
            <Document id="root_doc">
            <Schema name="programmeband_1" id="scpcct_1">
                <SimpleField name="N°" type="float"></SimpleField>
                <SimpleField name="Cooperative" type="string"></SimpleField>
                <SimpleField name="Code_CCC" type="string"></SimpleField>
                <SimpleField name="Code_Producteur" type="string"></SimpleField>
                <SimpleField name="Code_Parcelle" type="string"></SimpleField>
                <SimpleField name="Section" type="string"></SimpleField>
                <SimpleField name="Localite" type="string"></SimpleField>
                <SimpleField name="Sous-Prefecture" type="string"></SimpleField>
                <SimpleField name="Departement" type="string"></SimpleField>
                <SimpleField name="Region " type="string"></SimpleField>
                <SimpleField name="Nom" type="string"></SimpleField>
                <SimpleField name="Prenoms" type="string"></SimpleField>
                <SimpleField name="Genre" type="string"></SimpleField>
                <SimpleField name="Certification" type="string"></SimpleField>
                <SimpleField name="Programme" type="string"></SimpleField>
                <SimpleField name="Statut" type="string"></SimpleField>
                <SimpleField name="Field17" type="string"></SimpleField>
            </Schema>
            <Folder><name>programmeband_1</name>
            ';
            $i=1;
            foreach($parcelles as $data)
            {
                if($data->waypoints !="" || $data->waypoints !=null)
                {
                $datakml .= '<Placemark>
                <Style><LineStyle><color>ff0000ff</color></LineStyle><PolyStyle><fill>0</fill></PolyStyle></Style>
                <ExtendedData><SchemaData schemaUrl="#programmeband_1">
                    <SimpleData name="N°">'.$i.'</SimpleData>
                    <SimpleData name="Cooperative">'.$data->producteur->localite->section->cooperative->name.'</SimpleData>
                    <SimpleData name="Code_CCC">'.$data->producteur->localite->section->cooperative->codeCoop.'</SimpleData>
                    <SimpleData name="Code_Producteur">'.$data->producteur->codeProd.'</SimpleData>
                    <SimpleData name="Code_Parcelle">'.$data->codeParc.'</SimpleData>
                    <SimpleData name="Section">'.$data->producteur->localite->section->libelle.'</SimpleData>
                    <SimpleData name="Localite">'.$data->producteur->localite->nom.'</SimpleData>
                    <SimpleData name="Sous-Prefecture">'.$data->producteur->localite->section->sousPrefecture.'</SimpleData>
                    <SimpleData name="Region ">'.$data->producteur->localite->section->region.'</SimpleData>
                    <SimpleData name="Nom">'.$data->producteur->nom.'</SimpleData>
                    <SimpleData name="Prenoms">'.$data->producteur->prenoms.'</SimpleData>
                    <SimpleData name="Genre">'.$data->producteur->sexe.'</SimpleData>
                    <SimpleData name="Programme">'.$data->producteur->programme->libelle.'</SimpleData>
                    <SimpleData name="Departement">'.$data->producteur->localite->section->departement.'</SimpleData>
                    <SimpleData name="Statut">'.$data->producteur->statut.'</SimpleData>
                </SchemaData>
                </ExtendedData>
                  <MultiGeometry>
                    <Polygon>
                        <outerBoundaryIs>
                            <LinearRing>
                                <coordinates>'.$data->waypoints.'</coordinates>
                            </LinearRing>
                        </outerBoundaryIs>
                    </Polygon>
                </MultiGeometry>
              </Placemark>';
              $i++;
                }
            }
            $datakml .= '</Folder>
            </Document></kml>';
                                    file_put_contents($kml_file_path, $datakml);
                                    $headers = ['Content-Type: application/kml'];
                                    $fileName = time() . '.kml';
                                    return response()->download($kml_file_path, $fileName, $headers);
                                }
                            }

                        }

        return view('manager.parcelle.mapping', compact('pageTitle', 'sections', 'parcelles', 'localites', 'producteurs'));
    }
    public function mappingPolygone()
    {

        $manager   = auth()->user();

        $cooperatives = Cooperative::where('id', $manager->cooperative_id)->get();

        $sections = Section::where('cooperative_id', $manager->cooperative_id)->with('cooperative')->get();

        $localites = Localite::joinRelationship('section')
            ->where('cooperative_id', $manager->cooperative_id)
            ->when(request()->section, function ($query, $section) {
                $query->where('section_id', $section);
            })
            ->get();
        $producteurs = Producteur::joinRelationship('localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])
            ->when(request()->localite, function ($query, $localite) {
                $query->where('localite_id', $localite);
            })
            ->get();
        $parcelles = Parcelle::dateFilter()->latest('id')
            ->joinRelationship('producteur.localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['typedeclaration', 'GPS'],['waypoints','!=',""]])
            ->whereNotNull('waypoints')
            ->when(request()->section, function ($query, $section) {
                $query->where('section_id', $section);
            })
            ->when(request()->localite, function ($query, $localite) {
                $query->where('localite_id', $localite);
            })
            ->when(request()->producteur, function ($query, $producteur) {
                $query->where('producteur_id', $producteur);
            })
            ->with(['producteur.localite.section'])
            ->get();
        $total = count($parcelles);
        $pageTitle  = "Gestion de mapping des parcelles($total)";
        return view('manager.parcelle.mapping-trace', compact('pageTitle', 'sections', 'parcelles', 'localites', 'producteurs', 'cooperatives'));
    }

    public function create()
    {
        $pageTitle = "Ajouter un parcelle";
        $manager   = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $producteurs  = Producteur::joinRelationship('localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->with('localite')->get();
        $arbres = Agroespecesarbre::all();
        return view('manager.parcelle.create', compact('pageTitle', 'producteurs', 'localites', 'sections', 'arbres'));
    }

    public function uploadKML(Request $request)
    {
        $pageTitle = "Importation de fichier KML";
        $manager   = auth()->user();
        $i = 0;
        $k = 0;
        $parcel = "";
        if ($request->file('fichier_kml') != null) {
            $file = $request->file('fichier_kml');
            @unlink(public_path('upload/kml/'));
            $filename = $file->getClientOriginalName();
            $file->move(public_path('upload/kml'), $filename);
            $filePath = public_path('upload/kml/' . $filename);
            $dataPolygones = $this->getCoordinatesFromKML($filePath);
            $coordinates = $dataPolygones[0];

            foreach ($dataPolygones as $index => $data) {

                $producteur = Producteur::joinRelationship('localite.section')
                    ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->where('codeProd', $data['codeProducteur'])->first();
                if ($producteur == null) {
                    $producteur = new Producteur();
                }

                $section = Section::where([['cooperative_id', $manager->cooperative_id], ['libelle', $data['section']]])->first();
                if ($section == null) {
                    $section = new Section();
                    $data['section'] = $this->verifysection($data['section']);
                }
                $section->cooperative_id = $manager->cooperative_id;
                $section->region = $data['region'];
                $section->departement = $data['departement'];
                $section->sousPrefecture = $data['sousPrefecture'];
                $section->libelle = $data['section'];
                $section->save();

                $localite = Localite::joinRelationship('section')->where([['cooperative_id', $manager->cooperative_id], ['nom', $data['localite']]])->first();
                if ($localite == null) {
                    $localite = new Localite();
                    $data['localite'] = $this->verifylocalite($data['localite']);
                    $localite->codeLocal = $this->generelocalitecode($data['localite']);
                }
                $localite->nom = $data['localite'];
                $localite->section_id = $section->id;
                $localite->codeLocal = $localite->codeLocal;
                $localite->save();

                $programme = Programme::where('libelle', $data['programme'])->first();
                if ($programme != null) {
                    $producteur->programme_id = $programme->id;
                }
                $producteur->nom = utf8_encode($data['nom']);
                $producteur->prenoms = utf8_encode($data['prenoms']);
                $producteur->num_ccc = $data['codeCCC'];
                $producteur->sexe = $data['genre'];
                $producteur->statut = $data['candidat'];
                $producteur->codeProd = $data['codeProducteur'];
                $producteur->localite_id = $localite->id;
                $producteur->save();

                $certification = Certification::where('fullname', $data['certification'])->first();
                if ($certification == null) {
                    $certification = new Certification();
                    $certification->nom = $data['certification'];
                    $certification->fullname = $data['certification'];

                    $certification->save();
                    $prodcertif = Producteur_certification::where([['producteur_id', $producteur->id], ['certification', $certification->nom]])->first();
                    if ($prodcertif == null) {
                        $prodcertif = new Producteur_certification();
                    }
                    $prodcertif->producteur_id = $producteur->id;
                    $prodcertif->certification = $certification->nom;
                    $prodcertif->save();
                }


                $parcelle = Parcelle::where([['producteur_id', $producteur->id], ['codeParc', $data['codeParcelle']]])->first();

                if ($parcelle != null) {

                    $centroid = $this->calculateCentroid($data['coordinates']);
                    $superficie = substr($this->calculatePolygonArea($data['coordinates']), 0, 5);

                    $parcelle->producteur_id  = $producteur->id;
                    $parcelle->codeParc  = isset($data['codeParcelle']) ? $data['codeParcelle'] : null;
                    $parcelle->typedeclaration  = 'GPS';
                    $parcelle->culture  = 'CACAO';
                    $parcelle->superficie = round($superficie, 2);
                    //dd($parcelle->superficie);
                    $parcelle->latitude = round($centroid['lat'], 6);
                    $parcelle->longitude = round($centroid['lng'], 6);
                    $parcelle->waypoints = $data['coordinates'];
                    $parcelle->save();
                    $i++;
                } else {
                    $k++;
                    $parcel .= $data['codeParcelle'] . ',';
                }
            }

            $notify[] = ['success', "$i Polygones ont été importés avec succès et $k Polygones avec pour codes parcelles: $parcel n'ont importé."];

            return back()->withNotify($notify);
        }



        return view('manager.parcelle.uploadkml', compact('pageTitle'));
    }
    public function getCoordinatesFromKML($filePath)
    {
        // Charger le fichier KML
        $kmlContent = file_get_contents($filePath);

        // Créer un objet SimpleXML pour parcourir le fichier KML
        $kml = new SimpleXMLElement($kmlContent);

        // Initialiser un tableau pour stocker les coordonnées
        $dataArray = array();

        // Parcourir chaque Placemark dans le document KML
        foreach ($kml->Document->Folder->Placemark as $placemark) {
            // Récupérer les coordonnées de la balise <coordinates>
            $coordinates = (string)$placemark->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates;
            // $fieldID = (string)$placemark->ExtendedData->SchemaData->SimpleData[0];
            // $farmerID = (string)$placemark->ExtendedData->SchemaData->SimpleData[1];
            // $farmerName = (string)$placemark->ExtendedData->SchemaData->SimpleData[2];
            // $fieldName = (string)$placemark->ExtendedData->SchemaData->SimpleData[3];
            // $size = (string)$placemark->ExtendedData->SchemaData->SimpleData[4];
            // $supHa = (string)$placemark->ExtendedData->SchemaData->SimpleData[5];
            // $nOrdre = (string)$placemark->ExtendedData->SchemaData->SimpleData[6];
            // $cooperative = (string)$placemark->ExtendedData->SchemaData->SimpleData[7];
            // $codeCCC = (string)$placemark->ExtendedData->SchemaData->SimpleData[8];
            // $codeProducteur = (string)$placemark->ExtendedData->SchemaData->SimpleData[9];
            // $section = (string)$placemark->ExtendedData->SchemaData->SimpleData[10];
            // $localite = (string)$placemark->ExtendedData->SchemaData->SimpleData[11];
            // $sousPrefecture = (string)$placemark->ExtendedData->SchemaData->SimpleData[12];
            // $departement = (string)$placemark->ExtendedData->SchemaData->SimpleData[13];
            // $region = (string)$placemark->ExtendedData->SchemaData->SimpleData[14];
            // $prenoms = (string)$placemark->ExtendedData->SchemaData->SimpleData[15];
            // $nom = (string)$placemark->ExtendedData->SchemaData->SimpleData[16];
            // $genre = (string)$placemark->ExtendedData->SchemaData->SimpleData[17];
            // $certification = (string)$placemark->ExtendedData->SchemaData->SimpleData[22];
            // $programme = (string)$placemark->ExtendedData->SchemaData->SimpleData[23];
            $fieldID = "";
            $farmerID = "";
            $farmerName = "";
            $fieldName = "";
            $size = "";
            $nOrdre = "";
            //$supHa = (string)$placemark->ExtendedData->SchemaData->SimpleData[0];
            $supHa = 0;
            $cooperative = (string)$placemark->ExtendedData->SchemaData->SimpleData[1];
            $codeCCC = (string)$placemark->ExtendedData->SchemaData->SimpleData[2];
            // $codeProducteur = Str::before((string)$placemark->ExtendedData->SchemaData->SimpleData[3], " ");
            // $codeParcelle = Str::before((string)$placemark->ExtendedData->SchemaData->SimpleData[4], " ");
            $codeProducteur = (string)$placemark->ExtendedData->SchemaData->SimpleData[3];
            $codeParcelle = (string)$placemark->ExtendedData->SchemaData->SimpleData[4];
            $section = (string)$placemark->ExtendedData->SchemaData->SimpleData[5];
            $localite = (string)$placemark->ExtendedData->SchemaData->SimpleData[6];
            $sousPrefecture = (string)$placemark->ExtendedData->SchemaData->SimpleData[7];
            $departement = (string)$placemark->ExtendedData->SchemaData->SimpleData[8];
            $region = (string)$placemark->ExtendedData->SchemaData->SimpleData[9];
            $prenoms = (string)$placemark->ExtendedData->SchemaData->SimpleData[11];
            $nom = (string)$placemark->ExtendedData->SchemaData->SimpleData[10];
            $genre = (string)$placemark->ExtendedData->SchemaData->SimpleData[12];
            $candidat = (string)$placemark->ExtendedData->SchemaData->SimpleData[14];
            //$certification = "Rainforest Alliance";
            $certification = "";
            $programme = "Bandama";

            $supHa = Str::before($supHa, ' ');
            if (Str::contains($supHa, ",")) {
                $supHa = Str::replaceFirst(',', '.', $supHa);
                if (Str::contains($supHa, ",")) {
                    $supHa = Str::replaceFirst('m²', '', $supHa);
                }
            }
            //Récupérer les coordonnées de la balise

            // Ajouter les données au tableau
            $coordinatesArray[] = $coordinates;
            $dataArray[] = array(
                'coordinates' => $coordinates,
                'fieldID' => $fieldID,
                'farmerID' => $farmerID,
                'farmerName' => $farmerName,
                'fieldName' => $fieldName,
                'size' => $size,
                'nOrdre' => $nOrdre,
                'supHa' => trim($supHa),
                'cooperative' => trim($cooperative),
                'codeCCC' => trim($codeCCC),
                'codeProducteur' => trim($codeProducteur),
                'codeParcelle' => trim($codeParcelle),
                'section' => addslashes(trim(enleveaccents($section))),
                'localite' => addslashes(trim(enleveaccents($localite))),
                'sousPrefecture' => addslashes(trim(enleveaccents($sousPrefecture))),
                'departement' => addslashes(trim(enleveaccents($departement))),
                'region' => addslashes(trim(enleveaccents($region))),
                'prenoms' => addslashes(trim(enleveaccents($prenoms))),
                'nom' => addslashes(trim(enleveaccents($nom))),
                'genre' => ucfirst($genre),
                'certification' => $certification,
                'candidat' => $candidat,
                'programme' => $programme
            );
        }

        // Retourner le tableau des coordonnées
        return $dataArray;
    }

    private function calculateCentroid($coordinates)
    {
        /*
        Calcule le centroïde d'un polygone à partir de ses coordonnées.

        Args:
            $coordinates (str): Les coordonnées du polygone.

        Returns:
            str: Les coordonnées du centroïde.
        */
        // Convertir les coordonnées en une liste de tuples
        $coords = array_map(function ($coord) {
            return array_map('floatval', explode(',', $coord));
        }, explode(' ', $coordinates));

        // Calculer la somme des coordonnées
        $sum_x = array_sum(array_column($coords, 0));
        $sum_y = array_sum(array_column($coords, 1));

        // Calculer le centroïde
        $centroid_x = $sum_x / count($coords);
        $centroid_y = $sum_y / count($coords);

        // Retourner les coordonnées du centroïde

        return array('lat' => number_format($centroid_y, 6), 'lng' => number_format($centroid_x, 6));
    }


    // Fonction pour calculer la superficie du polygone
    private function calculatePolygonArea($coordinates)
    {
        /*
        Calcule l'aire d'un polygone à partir de ses coordonnées.

        Args:
            $coordinates (str): Les coordonnées du polygone.

        Returns:
            float: L'aire du polygone.
        */
        // Convertir les coordonnées en une liste de tuples
        $coords = array_map(function ($coord) {
            return array_map('floatval', explode(',', $coord));
        }, explode(' ', $coordinates));

        // Calculer l'aire
        $area = 0.0;
        for ($i = 0; $i < count($coords); $i++) {
            $j = ($i + 1) % count($coords);
            $area += $coords[$i][0] * $coords[$j][1] - $coords[$j][0] * $coords[$i][1];
        }
        $area /= 2.0;

        // Retourner l'aire
        return abs($area) * 0.0001;
    }

    private function verifylocalite($nom)
    {
        $action = 'non';
        do {
            $data = Localite::select('nom')->where('nom', $nom)->orderby('id', 'desc')->first();
            if ($data != '') {

                $nomLocal = $data->nom;
                $nom = Str::beforeLast($nomLocal, ' ');
                $chaine_number = Str::afterLast($nomLocal, ' ');

                if (is_numeric($chaine_number) && ($chaine_number < 10)) {
                    $zero = "00";
                } else if (is_numeric($chaine_number) && ($chaine_number < 100)) {
                    $zero = "0";
                } else {
                    $zero = "00";
                    $chaine_number = 0;
                }

                $sub = $nom . ' ';
                $lastCode = $chaine_number + 1;
                $nomLocal = $sub . $zero . $lastCode;
            } else {

                $nomLocal = $nom;
            }
            $verif = Localite::select('nom')->where('nom', $nomLocal)->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $nom = $verif->nom;
            }
        } while ($action != 'non');

        return $nomLocal;
    }
    private function generelocalitecode($name)
    {
        $action = 'non';
        do {

            $data = Localite::select('codeLocal')->where('nom', $name)->orderby('id', 'desc')->first();

            if ($data != '') {

                $code = $data->codeLocal;

                $chaine_number = Str::afterLast($code, '-');

                if ($chaine_number < 10) {
                    $zero = "00";
                } else if ($chaine_number < 100) {
                    $zero = "0";
                } else {
                    $zero = "";
                }
            } else {
                $zero = "00";
                $chaine_number = 0;
            }

            $abrege = Str::upper(Str::substr($name, 0, 3));
            $sub = $abrege . '-';
            $lastCode = $chaine_number + 1;
            $codeP = $sub . $zero . $lastCode;

            $verif = Localite::select('nom')->where('codeLocal', $codeP)->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $name = $verif->nom;
            }
        } while ($action != 'non');

        return $codeP;
    }

    private function verifysection($nom)
    {
        $action = 'non';
        do {
            $data = Section::select('libelle')->where([['cooperative_id', auth()->user()->cooperative_id], ['libelle', $nom]])->orderby('id', 'desc')->first();
            if ($data != '') {

                $nomSection = $data->libelle;
                $nom = Str::beforeLast($nomSection, ' ');
                $chaine_number = Str::afterLast($nomSection, ' ');

                if (is_numeric($chaine_number) && ($chaine_number < 10)) {
                    $zero = "00";
                } else if (is_numeric($chaine_number) && ($chaine_number < 100)) {
                    $zero = "0";
                } else {
                    $zero = "00";
                    $chaine_number = 0;
                }

                $sub = $nom . ' ';
                $lastCode = $chaine_number + 1;
                $nomSection = $sub . $zero . $lastCode;
            } else {

                $nomSection = $nom;
            }
            $verif = Section::select('libelle')->where([['cooperative_id', auth()->user()->cooperative_id], ['libelle', $nomSection]])->orderby('id', 'desc')->first();
            if ($verif == null) {
                $action = 'non';
            } else {
                $action = 'oui';
                $nom = $verif->libelle;
            }
        } while ($action != 'non');

        return $nomSection;
    }
    public function store(Request $request)
    {
        $validationRule = [
            'section' => 'required',
            'localite' => 'required',
            'producteur_id' => 'required',
            'anneeCreation' => 'required',
            'ageMoyenCacao' => 'required',
            'parcelleRegenerer' => 'required',
            'anneeRegenerer' => 'required_if:parcelleRegenerer,==,oui',
            'superficieConcerne' => 'required_if:parcelleRegenerer,==,oui',
            'typeDoc' => 'required',
            'presenceCourDeau' => 'required',
            'courDeau' => 'required_if:presenceCourDeau,==,oui',
            'autreCourDeau' => 'required_if:courDeau,==,Autre',
            'existeMesureProtection' => 'required',
            'existePente' => 'required',
            'superficie' => 'required',
            'nbCacaoParHectare' => 'required|numeric',
            'erosion' => 'required',
            'items.*.arbre'     => 'required|integer',
            'items.*.nombre'     => 'required|integer',
            'longitude' => 'numeric|nullable',
            'latitude' => 'numeric|nullable',
            'typedeclaration' => 'required',
        ];
        $messages = [
            'section.required' => 'Le champ section est obligatoire',
            'localite.required' => 'Le champ localité est obligatoire',
            'producteur_id.required' => 'Le champ producteur est obligatoire',
            'anneeCreation.required' => 'Le champ année de création est obligatoire',
            'ageMoyenCacao.required' => 'Le champ age moyen du cacao est obligatoire',
            'parcelleRegenerer.required' => 'Le champ parcelle à regénérer est obligatoire',
            'anneeRegenerer.required_if' => 'Le champ année de régénération est obligatoire',
            'superficieRegenerer.required_if' => 'Le champ superficie de régénération est obligatoire',
            'typeDoc.required' => 'Le champ type de document est obligatoire',
            'presenceCourDeau.required' => 'Le champ présence de cours d\'eau est obligatoire',
            'courDeau.required_if' => 'Le champ cours d\'eau est obligatoire',
            'existeMesureProtection.required' => 'Le champ existence de mesure de protection est obligatoire',
            'existePente.required' => 'Le champ existence de pente est obligatoire',
            'superficie.required' => 'Le champ superficie est obligatoire',
            'nbCacaoParHectare.required' => 'Le champ nombre de cacao par hectare est obligatoire',
            'superficieConcerne.required_if' => 'Le champ superficie concerné est obligatoire',
            'erosion.required' => 'Le champ érosion est obligatoire',
            'longitude.numeric' => 'Le champ longitude doit être un nombre décimal',
            'latitude.numeric' => 'Le champ latitude doit être un nombre décimal',
        ];
        $attributes = [
            'section' => 'section',
            'localite' => 'localité',
            'producteur_id' => 'producteur',
            'anneeCreation' => 'année de création',
            'ageMoyenCacao' => 'age moyen du cacao',
            'parcelleRegenerer' => 'parcelle regénéré',
            'anneeRegenerer' => 'L\'année de régénération',
            'superficieRegenerer' => 'superficie de régénérer',
            'typeDoc' => 'type de document',
            'presenceCourDeau' => 'présence de cours d\'eau',
            'courDeau' => 'cours d\'eau',
            'existeMesureProtection' => 'existence de mesure de protection',
            'existePente' => 'existence de pente',
            'superficie' => 'superficie',
            'nbCacaoParHectare' => 'nombre de cacao par hectare',
            'superficieConcerne' => 'superficie concerné',
            'erosion' => 'érosion',
        ];
        $request->validate($validationRule, $messages, $attributes);
        $manager = auth()->user();
        $localite = Localite::where('id', $request->localite)->first();

        if ($localite->status == Status::NO) {
            $notify[] = ['error', 'Cette localité est désactivé'];
            return back()->withNotify($notify)->withInput();
        }

        if ($request->id) {
            $parcelle = Parcelle::findOrFail($request->id);
            AutreAgroespecesarbreParcelle::where('parcelle_id', $parcelle->id)->delete();
            $codeParc = $parcelle->codeParc;
            if ($codeParc == '') {
                $produc = Producteur::joinRelationship('localite.section')
                    ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->select('codeProd')->find($request->producteur_id);
                if ($produc != null) {
                    $codeProd = $produc->codeProd;
                } else {
                    $codeProd = '';
                }
                $parcelle->codeParc  =  $this->generecodeparc($request->producteur_id, $codeProd);
            }
            $message = "La parcelle a été mise à jour avec succès";
        } else {
            $parcelle = new Parcelle();
            $produc = Producteur::joinRelationship('localite.section')
                ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->select('codeProd')->find($request->producteur_id);
            if ($produc != null) {
                $codeProd = $produc->codeProd;
            } else {
                $codeProd = '';
            }
            $parcelle->codeParc  =  $this->generecodeparc($request->producteur_id, $codeProd);
        }
        $parcelle->producteur_id  = $request->producteur_id;
        $parcelle->typedeclaration  = $request->typedeclaration;
        $parcelle->niveauPente  = $request->niveauPente;
        $parcelle->anneeCreation  = $request->anneeCreation;
        $parcelle->ageMoyenCacao  = $request->ageMoyenCacao;
        $parcelle->parcelleRegenerer  = $request->parcelleRegenerer;
        $parcelle->anneeRegenerer  = $request->anneeRegenerer;
        $parcelle->superficieConcerne  = $request->superficieConcerne;
        $parcelle->typeDoc  = $request->typeDoc;
        $parcelle->presenceCourDeau  = $request->presenceCourDeau;
        $parcelle->courDeau  = $request->courDeau;
        $parcelle->existeMesureProtection  = $request->existeMesureProtection;
        $parcelle->existePente  = $request->existePente;
        $parcelle->superficie  = $request->superficie;
        $parcelle->latitude  = $request->latitude;
        $parcelle->longitude  = $request->longitude;
        $parcelle->userid = auth()->user()->id;
        $parcelle->nbCacaoParHectare  = $request->nbCacaoParHectare;
        $parcelle->erosion  = $request->erosion;
        $parcelle->autreCourDeau = $request->autreCourDeau;
        $parcelle->autreProtection = $request->autreProtection;


        if ($request->hasFile('fichier_kml_gpx')) {
            try {
                $parcelle->fichier_kml_gpx = $request->file('fichier_kml_gpx')->store('public/parcelles/kmlgpx');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Impossible de télécharger votre image'];
                return back()->withNotify($notify);
            }
        }
        $parcelle->save();
        if ($parcelle != null) {
            $id = $parcelle->id;
            $datas  = $data2 = [];
            if (($request->protection != null)) {
                Parcelle_type_protection::where('parcelle_id', $id)->delete();
                $i = 0;
                foreach ($request->protection as $protection) {
                    if (!empty($protection)) {
                        $datas[] = [
                            'parcelle_id' => $id,
                            'typeProtection' => $protection,
                        ];
                    }

                    $i++;
                }
                Parcelle_type_protection::insert($datas);
            }
            if (($request->items != null)) {
                agroespeceabre_parcelle::where('parcelle_id', $id)->delete();
                foreach ($request->items as $item) {

                    $data2[] = [
                        'parcelle_id' => $id,
                        'nombre' => $item['nombre'],
                        'agroespeceabre_id' => $item['arbre'],
                    ];
                }
                agroespeceabre_parcelle::insert($data2);
            }
            if($request->arbreStrate != null){
                AutreAgroespecesarbreParcelle::where('parcelle_id', $id)->delete();
                foreach ($request->arbreStrate as $arbreStrate) {
                    $agroespeceabre = Agroespecesarbre::firstOrCreate([
                        'nom' => $arbreStrate['nom'],
                        'strate' => $arbreStrate['strate'],
                    ]);
                    AutreAgroespecesarbreParcelle::create([
                        'agroespeceabre_id' => $agroespeceabre->id,
                        'parcelle_id' => $id,
                        'nom' => $arbreStrate['nom'],
                        'nombre' => $arbreStrate['qte'],
                        'strate' => $arbreStrate['strate'],
                    ]);
                }
            }
            if ($request->varietes != null) {
                VarieteParcelle::where('parcelle_id', $id)->delete();
                foreach ($request->varietes as $variete) {
                    $data[] = [
                        'parcelle_id' => $id,
                        'variete' => $variete,
                    ];
                }
                VarieteParcelle::insert($data);
            }
        }


        $notify[] = ['success', isset($message) ? $message : 'Le parcelle a été crée avec succès.'];
        return back()->withNotify($notify);
    }
    // private function generecodeparc($idProd, $codeProd)
    // {
    //     if ($codeProd) {
    //         $action = 'non';

    //         $data = Parcelle::select('codeParc')->where([
    //             ['producteur_id', $idProd],
    //             ['codeParc', '!=', null]
    //         ])->orderby('id', 'desc')->first();

    //         if ($data != '') {

    //             $code = $data->codeParc;

    //             if ($code != '') {
    //                 $chaine_number = Str::afterLast($code, '-');
    //                 $numero = Str::after($chaine_number, 'P');
    //                 $numero = $numero + 1;
    //             } else {
    //                 $numero = 1;
    //             }
    //             $codeParc = $codeProd . '-P' . $numero;

    //             do {

    //                 $verif = Parcelle::select('codeParc')->where('codeParc', $codeParc)->orderby('id', 'desc')->first();
    //                 if ($verif == null) {
    //                     $action = 'non';
    //                 } else {
    //                     $action = 'oui';
    //                     $code = $data->codeParc;

    //                     if ($code != '') {
    //                         $chaine_number = Str::afterLast($code, '-');
    //                         $numero = Str::after($chaine_number, 'P');
    //                         $numero = $numero + 1;
    //                     } else {
    //                         $numero = 1;
    //                     }
    //                     $codeParc = $codeProd . '-P' . $numero;
    //                 }
    //             } while ($action != 'non');
    //         } else {
    //             $codeParc = $codeProd . '-P1';
    //         }
    //     } else {
    //         $codeParc = '';
    //     }

    //     return $codeParc;
    // }
    private function generecodeparc($idProd, $codeProd)
    {
        if ($codeProd) {
            $action = 'non';
            $data = Parcelle::where('producteur_id', $idProd)->get();
            if ($data != '') {
                $nombreParcelles = $data->count();
                $numero = $nombreParcelles + 1;
                $codeParc = $codeProd . '-P' . $numero;
                // $codeProd = Str::beforeLast($codeProd, '-');
                // $codeParc = $codeProd . '-P' . $numero;
                // do {
                //     $verif = Parcelle::select('codeParc')->where('codeParc', $codeParc)->orderby('id', 'desc')->first();
                //     if ($verif == null) {
                //         $action = 'non';
                //     } else {
                //         $action = 'oui';
                //         $numero++;
                //         $codeParc = $codeProd . '-P' . $numero;
                //     }
                // } while ($action != 'non');
            } else {
                $codeParc = $codeProd . '-P1';
            }
        } else {
            $codeParc = '';
        }
        return $codeParc;
    }


    public function edit($id)
    {
        $pageTitle = "Mise à jour de la parcelle";
        $parcelle   = Parcelle::findOrFail($id);
        $manager   = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $producteurs  = Producteur::joinRelationship('localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->with('localite')->get();
        $protections = $parcelle->parcelleTypeProtections->pluck('typeProtection')->all();
        $arbres = Agroespecesarbre::all();
        $agroespeceabreParcelle = agroespeceabre_parcelle::where('parcelle_id', $id)->get();
        $autreAgroespecesarbreParcelle = $parcelle->autreAgroespecesarbreParcelles;

        return view('manager.parcelle.edit', compact('pageTitle', 'localites', 'parcelle', 'producteurs', 'sections', 'protections', 'arbres', 'agroespeceabreParcelle', 'autreAgroespecesarbreParcelle'));
    }
    public function show($id)
    {
        $pageTitle = "Détails de la parcelle";
        $manager   = auth()->user();
        $cooperatives = Cooperative::where('id', $manager->cooperative_id)->get();
        $parcelle   = Parcelle::with('agroespeceabre_parcelles.agroespeceabre')->find($id);
        $section = $parcelle->producteur->localite->section;
        $localite = $parcelle->producteur->localite;
        $producteur = $parcelle->producteur;
        $arbres = $parcelle->agroespeceabre_parcelles;
        // $arbres = $parcelle->agroespeceabre_parcelles->all();
        return view('manager.parcelle.show', compact('pageTitle', 'localite', 'parcelle', 'producteur', 'section', 'arbres', 'cooperatives'));
    }


    public function status($id)
    {
        return Parcelle::changeStatus($id);
    }


    public function exportExcel()
    {
        $filename = 'parcelles-' . gmdate('dmYhms') . '.xlsx';
        return Excel::download(new ExportParcelles, $filename);
    }

    public function  uploadContent(Request $request)
    {
        Excel::import(new ParcelleImport, $request->file('uploaded_file'));
        return back();
    }

    public function delete($id)
    {
        Parcelle::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
