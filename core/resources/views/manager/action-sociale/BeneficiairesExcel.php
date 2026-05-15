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
            <td>CODE ACTION SOCIALE</td>
            <td>BENEFICIARE</td>
        </tr>
        <?php
        foreach ($beneficiares as $c) { ?>
            <tr>
                <td><?php echo $c->id; ?></td>
                <td><?php echo $c->actionSociale->cooperative->name; ?></td>
                <td><?php echo $c->actionSociale->code; ?></td>
                <td><?php echo $c->localite->nom; ?></td>
            </tr>
        <?php } ?>
    </thead>
    <tbody>

</table>