<style>
    #categories {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #categories td, #categories th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #categories tr:nth-child(even){background-color: #f2f2f2;}

    #categories tr:hover {background-color: #ddd;}

    #categories th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>

<table id="categories" width="100%">
    <thead>
    <tr>
        <td>ID</td>
        <td>cooperative</td>
        <td>section</td>
        <td>localite</td>
        <td>programme_id</td>
        <td>nom</td>
        <td>prenoms</td>
        <td>code Producteur</td>
        <td>code Producteur app</td>
        <td>sexe</td>
        <td>date naissance</td>
        <td>phone1</td>
        <td>phone2</td>
        <td>nationalite</td>
        <td>autre membre</td>
        <td>autre phone</td>
        <td>niveau etude</td>
        <td>numero piece</td>
        <td>consentement</td>
        <td>statut matrimonial</td>
        <td>proprietaires</td>
        <td>plante partage</td>
        <td>habitation producteur</td>
        <td>annee fin</td>
        <td>annee demarrage</td>
        <td>numero ccc</td>
        <td>numero CMU</td>
        <td>carte CMU</td>
        <td>numero securite sociale</td>
        <td>type carte securite sociale</td>
        <td>type piece</td>
        <td>statut</td>
        <td>annee certification</td>
        <td>Date enreg</td> 
    </tr>
    </thead> 
    <?php
 
    foreach($producteurs as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo @$c->localite->section->cooperative->name; ?></td>
            <td><?php echo @$c->localite->section->libelle; ?></td>
            <td><?php echo @$c->localite->nom; ?></td>
            <td><?php echo @$c->programme->libelle; ?></td>
            <td><?php echo $c->nom; ?></td>
            <td><?php echo $c->prenoms; ?></td>
            <td><?php echo $c->codeProd; ?></td>
            <td><?php echo $c->codeProdapp; ?></td>
            <td><?php echo $c->sexe; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->dateNaiss)); ?></td>
            <td><?php echo $c->phone1; ?></td>
            <td><?php echo $c->phone2; ?></td>
            <td><?php echo $c->country->nationalite ?? null; ?></td>
            <td><?php echo $c->autreMembre; ?></td>
            <td><?php echo $c->autrePhone; ?></td>
            <td><?php echo $c->niveau_etude; ?></td>
            <td><?php echo $c->numPiece; ?></td> 
            <td><?php echo $c->consentement; ?></td>
            <td><?php echo $c->statutMatrimonial; ?></td>
            <td><?php echo $c->proprietaires; ?></td>
            <td><?php echo $c->plantePartage; ?></td>
            <td><?php echo $c->habitationProducteur; ?></td>
            <td><?php echo $c->anneeFin; ?></td>
            <td><?php echo $c->anneeDemarrage; ?></td>
            <td><?php echo $c->num_ccc; ?></td>
            <td><?php echo $c->numCMU; ?></td>
            <td><?php echo $c->carteCMU; ?></td>
            <td><?php echo $c->numSecuriteSociale; ?></td>
            <td><?php echo $c->typeCarteSecuriteSociale; ?></td>
            <td><?php echo $c->type_piece; ?></td>
            <td><?php echo $c->statut; ?></td>
            <td><?php echo $c->certificat; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>