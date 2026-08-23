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

    #categories tr:nth-child(even) {
        background-color: #f2f2f2;
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
            <td>Agent collecteur</td>
            <td>Cooperative</td>
            <td>Campagne</td>
            <td>Lieu formation</td>
            <td>Date debut</td>
            <td>Date fin</td>
            <td>Duree</td>
            <td>Modules</td>
            <td>Themes</td>
            <td>Entreprises</td>
            <td>Formateurs</td>
            <td>Employes presents</td>
            <td>Visiteurs</td>
            <td>Observation</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($formations as $formation)
            <tr>
                <td>{{ $formation->id }}</td>
                <td>{{ export_collecting_agent($formation) }}</td>
                <td>{{ optional($formation->cooperative)->name }}</td>
                <td>{{ optional($formation->campagne)->nom }}</td>
                <td>{{ $formation->lieu_formation }}</td>
                <td>{{ $formation->date_debut_formation ? date('d-m-Y', strtotime($formation->date_debut_formation)) : '' }}</td>
                <td>{{ $formation->date_fin_formation ? date('d-m-Y', strtotime($formation->date_fin_formation)) : '' }}</td>
                <td>{{ $formation->duree_formation }}</td>
                <td>{{ $formation->formationStaffModuleTheme->pluck('moduleFormationStaff.nom')->filter()->unique()->implode(', ') }}</td>
                <td>{{ $formation->formationStaffModuleTheme->pluck('themeFormationStaff.nom')->filter()->unique()->implode(', ') }}</td>
                <td>{{ $formation->formationStaffEntrepriseFormateur->pluck('entreprise.nom_entreprise')->filter()->unique()->implode(', ') }}</td>
                <td>
                    {{ $formation->formationStaffEntrepriseFormateur->map(function ($item) {
                        return trim(optional($item->formateurStaff)->nom_formateur . ' ' . optional($item->formateurStaff)->prenom_formateur);
                    })->filter()->unique()->implode(', ') }}
                </td>
                <td>
                    {{ $formation->staffListes->map(function ($item) {
                        return trim(optional($item->user)->lastname . ' ' . optional($item->user)->firstname);
                    })->filter()->unique()->implode(', ') }}
                </td>
                <td>{{ $formation->visiteurs->pluck('visiteur')->filter()->unique()->implode(', ') }}</td>
                <td>{{ $formation->observation_formation }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
