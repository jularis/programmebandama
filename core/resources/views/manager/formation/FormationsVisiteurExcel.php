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
        <td>Nom</td> 
        <td>Prenom</td>
        <td>Sexe</td>
        <td>Telephone</td>
    </tr>
    </thead> 
    <?php
 
    foreach($visiteurs as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->suivi_formation_id; ?></td> 
            <td><?php echo $c->nom ; ?></td>  
            <td><?php echo $c->prenom ; ?></td>
            <td><?php echo $c->sexe ; ?></td>
            <td><?php echo $c->telephone ; ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>