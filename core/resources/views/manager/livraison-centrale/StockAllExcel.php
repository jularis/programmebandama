<table id="categories" width="100%">
    <thead>
    <tr>
        <td>ID</td>
        <td>Cooperative</td>
        <td>Campagne</td>
        <td>Periode</td>
        <td>Magasin Section</td>
        <td>Magasin Central</td>
        <td>Entreprise</td>
        <td>Transporteur</td>
        <td>Vehicule</td>
        <td>Remorque</td>
        <td>Code connaissement</td>
        <td>Date livraison</td>
        <td>Quantite livrée</td>
        <td>Quantite sortie</td>
        <td>Quantite restante</td>
        <td>Date enreg</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach($stockscentral as $c): ?>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo $c->cooperative ? $c->cooperative->name : ''; ?></td>
            <td><?php echo $c->campagne ? $c->campagne->nom : ''; ?></td>
            <td><?php echo $c->campagnePeriode ? $c->campagnePeriode->nom : ''; ?></td>
            <td><?php echo $c->magasinSection ? $c->magasinSection->nom : ''; ?></td>
            <td><?php echo $c->magasinCentral ? $c->magasinCentral->nom : ''; ?></td>
            <td><?php echo ($c->transporteur && $c->transporteur->entreprise) ? $c->transporteur->entreprise->nom_entreprise : ''; ?></td>
            <td><?php echo $c->transporteur ? $c->transporteur->nom . ' ' . $c->transporteur->prenoms : ''; ?></td>
            <td><?php echo $c->vehicule ? $c->vehicule->vehicule_immat : ''; ?></td>
            <td><?php echo $c->remorque ? $c->remorque->remorque_immat : ''; ?></td>
            <td><?php echo $c->numero_connaissement; ?></td>
            <td><?php echo $c->date_livraison ? date('d-m-Y', strtotime($c->date_livraison)) : ''; ?></td>
            <td><?php echo $c->stocks_mag_entrant; ?></td>
            <td><?php echo $c->stocks_mag_sortant; ?></td>
            <td><?php echo $c->stocks_mag_entrant - $c->stocks_mag_sortant; ?></td>
            <td><?php echo $c->created_at ? date('d-m-Y', strtotime($c->created_at)) : ''; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
