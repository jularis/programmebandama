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
        <td>Categorie</td>
        <td>Question</td>
        <td>Notation</td>
        <td>Commentaire</td>
        <td>Recommandations</td>
        <td>Délai d'exécution</td>
        <td>Date de vérification</td>
        <td>Statut</td>
    </tr>
    </thead> 
    <?php
    foreach($questions as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->inspection_id; ?></td>
            <td><?php echo $c->questionnaire->categorieQuestion->titre; ?></td>
            <td><?php echo $c->questionnaire->nom; ?></td>
            <td><?php echo $c->notation; ?></td>
            <td><?php echo $c->commentaire; ?></td>
            <td><?php echo $c->recommandations; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->delai)); ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->date_verification)); ?></td>
            <td><?php echo $c->statuts; ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>