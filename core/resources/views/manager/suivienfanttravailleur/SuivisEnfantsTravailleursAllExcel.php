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
        <td>Code Enfant</td>
        <td>Nom Enfant</td>
        <td>Date Enquete</td>
        <td>Nom Enqueteur</td>
        <td>Situation Scolaire</td>
        <td>Extrait Naissance</td>
        <td>Etat Soumission</td>
        <td>Date enreg</td>
    </tr>
    </thead>
    <?php
    foreach($suivis as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo @$c->enfant->codeEnfant; ?></td>
            <td><?php echo $c->nom; ?></td>
            <td><?php echo $c->dateEnquete; ?></td>
            <td><?php echo $c->nomEnqueteur; ?></td>
            <td><?php echo $c->situationScolaire; ?></td>
            <td><?php echo $c->extraitNaissance; ?></td>
            <td><?php echo $c->etatSoumission; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>
