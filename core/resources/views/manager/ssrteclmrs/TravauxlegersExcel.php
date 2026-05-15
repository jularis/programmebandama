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
        <td>Nom membre</td>
        <td>Prenoms membre</td>
        <td>Code Membre</td> 
        <td>Travaux</td>
    </tr>
    </thead> 
    <?php
    foreach($travaux as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo $c->ssrteclmrs->nomMembre; ?></td>
            <td><?php echo $c->ssrteclmrs->prenomMembre; ?></td>
            <td><?php echo $c->ssrteclmrs->codeMembre; ?></td> 
            <td><?php echo $c->travauxlegers; ?></td> 
            
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>