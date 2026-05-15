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
        <td>Section</td>
        <td>Localite</td>
        <td>Nom</td>
        <td>Prenoms</td>
        <td>Code Prod</td>
        <td>main Oeuvre Familial</td>
<td>travailleur amilial</td>
<td>societe Travail</td>
<td>nombre Personne</td>
<td>autre Banque</td>
<td>nom Banque</td>
<td>autre Activite</td>
<td>forets jachere</td>
<td>superficie</td>
<td>autres Cultures</td>
<td>travailleurs</td>
<td>travailleurs permanents</td>
<td>travailleurs temporaires</td>
<td>compte Banque</td>
<td>mobile Money</td>

    </tr>
    </thead>
    <?php
    foreach($infos as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo $c->producteur->localite->section->cooperative->name  ?? ""; ?></td>
            <td><?php echo $c->producteur->localite->section->libelle ?? ""; ?></td>
            <td><?php echo $c->producteur->localite->nom  ?? ""; ?></td>
            <td><?php echo $c->producteur->nom  ?? ""; ?></td>
            <td><?php echo $c->producteur->prenoms  ?? ""; ?></td>
            <td><?php echo $c->producteur->codeProd  ?? "" ; ?></td>
            <td><?php echo $c->mainOeuvreFamilial  ?? "" ; ?></td>
<td><?php echo $c->travailleurFamilial  ?? "" ; ?></td>
<td><?php echo $c->societeTravail  ?? "" ; ?></td>
<td><?php echo $c->nombrePersonne  ?? "" ; ?></td>
<td><?php echo $c->autreBanque  ?? "" ; ?></td>
<td><?php echo $c->nomBanque  ?? "" ; ?></td>
<td><?php echo $c->autreActivite  ?? "" ; ?></td>
<td><?php echo $c->foretsjachere  ?? "" ; ?></td>
<td><?php echo $c->superficie  ?? "" ; ?></td>
<td><?php echo $c->autresCultures  ?? "" ; ?></td>
<td><?php echo $c->travailleurs  ?? "" ; ?></td>
<td><?php echo $c->travailleurspermanents  ?? "" ; ?></td>
<td><?php echo $c->travailleurstemporaires  ?? "" ; ?></td>
<td><?php echo $c->compteBanque  ?? "" ; ?></td>
<td><?php echo $c->mobileMoney  ?? "" ; ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>