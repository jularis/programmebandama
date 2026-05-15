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
        <td>Cooperative</td>
        <td>Role</td>
        <td>Type de compte</td>
        <td>Username</td> 
        <td>Nom</td>
        <td>Prenoms</td> 
        <td>Email</td> 
        <td>Phone</td>
        <td>Adresse</td> 
        <td>Date enreg</td> 
    </tr>
    </thead> 
    <?php
    foreach($staffs as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo $c->cooperative->name; ?></td>
            <td><?php 
            if(!empty($c->getRoleNames()))
            {
                $roles = array();
            foreach($c->getRoleNames() as $v)
                {
                    $roles[] = $v; 
                }
                echo implode(', ',$roles);
            }
            ?></td>
            <td><?php echo $c->type_compte; ?></td>
            <td><?php echo $c->username; ?></td>
            <td><?php echo $c->lastname; ?></td>
            <td><?php echo $c->firstname; ?></td>
            <td><?php echo $c->email; ?></td>
            <td><?php echo $c->mobile; ?></td>
            <td><?php echo $c->adresse; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>