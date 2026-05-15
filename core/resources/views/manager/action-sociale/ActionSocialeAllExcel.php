<style>
    #categories {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #categories td,
    #categories th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #categories tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #categories tr:hover {
        background-color: #ddd;
    }

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
            <td>COOPERATIVE</td>
            <td>CODE</td>
            <td>TYPE DE PROJET</td>
            <td>NIVEAU DE REALISATION</td>
            <td>DATE DE DEMARRAGE</td>
            <td>DATE DE FIN</td>
            <td>COUT DU PROJET</td>
            <td>DATE DE LIVRAISON</td>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($actions as $c) {
        ?>
            <tr>
                <td><?php echo $c->id; ?></td>
                <td><?php echo $c->cooperative->name; ?></td>
                <td><?php echo $c->code; ?></td>
                <td><?php echo $c->type_projet; ?></td>
                <td><?php echo $c->niveau_realisation; ?></td>
                <td><?php echo $c->date_demarrage; ?></td>
                <td><?php echo $c->date_fin_projet; ?></td>
                <td><?php echo $c->cout_projet; ?></td>
                <td><?php echo $c->date_livraison; ?></td>
            </tr>
        <?php } ?>
</table>