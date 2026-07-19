<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateEnqueteMenageReferenceTables extends Migration
{
    protected $seeds = [
        'raisons_non_scolarisations' => [
            "Trop jeune (< 6 ans)",
            "Absence d'école / École trop loin",
            "Manque de capacité d'accueil de l'école",
            "Insécurité sur le chemin de l'école",
            "Les parents ne peuvent pas payer les frais",
            "Mauvais résultats scolaires",
            "Handicap ou maladie invalidante",
            "Pour aider mes parents",
            "Négligence des parents",
            "Manque d'extrait de naissance",
            "Apprendre un métier",
            "Pour travailler au champ",
            "Autre",
        ],
        'raisons_pas_extraits' => [
            "Ce n'est pas important",
            "Père n'a pas de papier",
            "Mère n'a pas de papier",
            "Lieu de déclaration éloigné",
            "Négligence",
            "On ne sait pas qui est le père / la mère",
            "Ne sait pas",
        ],
        'situations_pftes' => [
            "L'enfant travaille de longues heures",
            "L'enfant fait du défrichage",
            "L'enfant abat des arbres",
            "L'enfant brûle des parcelles",
            "L'enfant dessouche",
            "L'enfant produit du charbon de bois",
            "L'enfant chasse des gibiers",
            "L'enfant fait du bûcheronnage",
            "L'enfant pratique la trouaison",
            "L'enfant utilise un objet tranchant",
            "L'enfant manipule des produits chimiques",
            "L'enfant conduit un engin motorisé",
            "L'enfant porte des charges lourdes",
            "L'enfant n'a pas été déclaré pour l'acquisition d'un acte de naissance",
            "L'enfant n'est pas scolarisé alors qu'il a l'âge de l'être",
            "L'enfant n'est pas bien nourri",
            "L'enfant n'est pas épanoui",
            "L'enfant fait du travail de nuit",
            "L'enfant est victime de maltraitance physique (il est fréquemment battu)",
            "L'enfant est victime de maltraitance morale (il est fréquemment intimidé, injurié, déshonoré)",
            "L'enfant est victime de travail forcé",
            "L'enfant est impliqué dans un conflit armé et activités assimilées",
            "L'enfant est victime d'abus ou d'exploitation sexuelle (pornographie, prostitution forcée, proxénétisme)",
            "L'enfant est impliqué dans la production, le trafic ou l'utilisation de la drogue",
            "L'enfant est victime d'esclavage",
            "L'enfant est victime de traite",
            "L'enfant est victime de servitude",
            "L'enfant ne travaille pas",
            "Aucune",
        ],
        'raisons_travail_abus' => [
            "Pour l'argent de poche",
            "Pour aider les parents",
            "L'enfant y est contraint",
            "Manque de main-d'œuvre",
            "C'est lui-même qui le souhaite",
            "Non applicable",
            "Autre",
        ],
        'mesures_enfants' => [
            "Aucune mesure",
            "Sensibilisation",
            "Kit scolaire",
            "Uniforme scolaire",
            "Frais de scolarisation",
            "Serviettes hygiéniques",
            "Inscription dans un groupe de lecture",
            "Inscription dans une classe passerelle",
            "Acte de naissance (jugement supplétif)",
            "Apprentissage de métier (individuel)",
            "Formation professionnelle (préciser)",
            "Suivi scolaire individuel",
            "Autre (préciser)",
        ],
        'mesures_menages' => [
            "Aucune mesure",
            "Sensibilisation",
            "Foyer amélioré",
            "Pelle bongo",
            "Brouette",
            "AGR (préciser)",
            "Appui financier systématique",
            "Alphabétisation",
            "Accès à un Groupement de Services",
            "Autre (préciser)",
        ],
        'mesures_communautes' => [
            "Aucune mesure",
            "Sensibilisation",
            "Construction/rénovation d'école primaire",
            "Construction/rénovation d'école secondaire",
            "Construction/rénovation de cantine",
            "Construction/rénovation de latrines",
            "Construction de logement pour enseignant",
            "Réalisation de point d'eau villageois",
            "Groupement de Services Communautaires",
            "Construction/rénovation de centre de santé",
            "Alphabétisation",
            "Classe passerelle",
            "Autre (préciser)",
        ],
        'sensibilisation_themes' => [
            "Port de charges lourdes",
            "Défrichage",
            "Taille, récolte ou écabossage avec objet tranchant",
            "Dessouchage",
            "Abattage des arbres",
            "Brûlage des parcelles",
            "Bûcheronnage",
            "Autres thèmes",
        ],
        'sensibilisation_outils' => [
            "Boites à images",
            "Affiche",
            "Flyers",
            "Vidéo/Film",
            "Séquence audio/radiophonique",
            "Sketch / Théâtre / Représentation scénique",
            "Messages oraux / échanges",
            "Autre",
        ],
    ];

    public function up()
    {
        foreach ($this->seeds as $tableName => $values) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->increments('id');
                $table->string('nom', 255);
                $table->integer('status')->default(1);
                $table->timestamps();
            });

            $now = date('Y-m-d H:i:s');
            $rows = array_map(function ($nom) use ($now) {
                return ['nom' => $nom, 'status' => 1, 'created_at' => $now, 'updated_at' => $now];
            }, $values);

            DB::table($tableName)->insert($rows);
        }
    }

    public function down()
    {
        foreach (array_keys($this->seeds) as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
}
