<!-- SETTINGS SIDEBAR START -->
<div class="mobile-close-overlay w-100 h-100" id="close-settings-overlay"></div>
<div class="settings-sidebar bg-white py-3" id="mob-settings-sidebar">
    <a class="d-block d-lg-none close-it" id="close-settings"><i class="fa fa-times"></i></a>

    <!-- SETTINGS SEARCH START -->
    <form class="border-bottom-grey px-4 pb-3 d-flex">
        <div class="input-group rounded py-1 border-grey">
            <div class="input-group-prepend">
                <span class="input-group-text border-0 bg-white">
                    <i class="fa fa-search f-12 text-lightest"></i>
                </span>
            </div>
            <input type="text" id="search-setting-menu" class="form-control border-0 f-12 pl-0"
                   placeholder="@lang('app.search')">
        </div>
    </form>
    <!-- SETTINGS SEARCH END -->

    <!-- SETTINGS MENU START -->
    <ul class="settings-menu" id="settingsMenu">
    <x-setting-menu-item :active="$activeMenu" menu="cooperative_settings" :href="route('manager.settings.cooperative-settings.index')" :text="__('Coopérative')"/>
    <x-setting-menu-item :active="$activeMenu" menu="section_settings" :href="route('manager.settings.section-settings.index')" :text="__('Section')"/>
    <x-setting-menu-item :active="$activeMenu" menu="localite_settings" :href="route('manager.settings.localite-settings.index')" :text="__('Localite')"/>
    <x-setting-menu-item :active="$activeMenu" menu="departement_settings" :href="route('manager.settings.departements.index')" :text="__('Départements')"/> 
    <x-setting-menu-item :active="$activeMenu" menu="designation_settings" :href="route('manager.settings.designations.index')" :text="__('Désignations')"/> 
    <x-setting-menu-item :active="$activeMenu" menu="entreprise_settings" :href="route('manager.settings.entreprise.index')" :text="__('Entreprise')"/> 
    <x-setting-menu-item :active="$activeMenu" menu="formateur_settings" :href="route('manager.settings.formateurStaff.list')" :text="__('Formateur')"/>
    <x-setting-menu-item :active="$activeMenu" menu="designation_settings" :href="route('manager.settings.designations.index')" :text="__('Postes occupés')"/> 
    <x-setting-menu-item :active="$activeMenu" menu="attendance_settings" :href="route('manager.settings.attendance-settings.index')" :text="__('Présences')"/>
    <x-setting-menu-item :active="$activeMenu" menu="leave_settings" :href="route('manager.settings.leaves-settings.index')" :text="__('Congés')"/>
    <x-setting-menu-item :active="$activeMenu" menu="holidays_settings" :href="route('manager.holidays.index')" :text="__('Jours Fériés')"/>
    <x-setting-menu-item :active="$activeMenu" menu="magasinSection_settings" :href="route('manager.settings.magasinSection.index')" :text="__('Magasins Sections')"/> 
    <x-setting-menu-item :active="$activeMenu" menu="magasinCentral_settings" :href="route('manager.settings.magasinCentral.index')" :text="__('Magasins Centraux')"/>
    <x-setting-menu-item :active="$activeMenu" menu="transporteur_settings" :href="route('manager.settings.transporteur.index')" :text="__('Transporteurs')"/> 
    <x-setting-menu-item :active="$activeMenu" menu="vehicule_settings" :href="route('manager.settings.vehicule.index')" :text="__('Véhicules')"/>
    <x-setting-menu-item :active="$activeMenu" menu="remorque_settings" :href="route('manager.settings.remorque.index')" :text="__('Remorques')"/>  
    <x-setting-menu-item :active="$activeMenu" menu="travauxDangereux_settings" :href="route('manager.settings.travauxDangereux.index')" :text="__('Travaux Dangereux')"/>
    <x-setting-menu-item :active="$activeMenu" menu="travauxLegers_settings" :href="route('manager.settings.travauxLegers.index')" :text="__('Travaux Legers')"/>
    <x-setting-menu-item :active="$activeMenu" menu="arretEcole_settings" :href="route('manager.settings.arretEcole.index')" :text="__('Arrets Ecole')"/>
    <x-setting-menu-item :active="$activeMenu" menu="typeFormation_settings" :href="route('manager.settings.typeFormation.index')" :text="__('Types de Formation')"/>
    <x-setting-menu-item :active="$activeMenu" menu="themeFormation_settings" :href="route('manager.settings.themeFormation.index')" :text="__('Themes de Formation')"/>
    <x-setting-menu-item :active="$activeMenu" menu="sousThemeFormation_settings" :href="route('manager.settings.sousThemeFormation.index')" :text="__('Sous theme formation')"/>
    <x-setting-menu-item :active="$activeMenu" menu="moduleFormationStaff_settings" :href="route('manager.settings.moduleFormationStaff.index')" :text="__('Modules de Formation Staffs')"/>
    <x-setting-menu-item :active="$activeMenu" menu="themeFormationStaff_settings" :href="route('manager.settings.themeFormationStaff.index')" :text="__('Themes de Formation Staffs')"/>
    <x-setting-menu-item :active="$activeMenu" menu="categorieQuestionnaire_settings" :href="route('manager.settings.categorieQuestionnaire.index')" :text="__('Categorie Questionnaire')"/>
    <x-setting-menu-item :active="$activeMenu" menu="questionnaire_settings" :href="route('manager.settings.questionnaire.index')" :text="__('Questionnaire')"/>
    <x-setting-menu-item :active="$activeMenu" menu="especeArbre_settings" :href="route('manager.settings.especeArbre.index')" :text="__('Espèces Arbres')"/>
    <x-setting-menu-item :active="$activeMenu" menu="typeArchive_settings" :href="route('manager.settings.typeArchive.index')" :text="__('Type Archives')"/> 

    </ul>
    <!-- SETTINGS MENU END -->

</div>
<!-- SETTINGS SIDEBAR END -->

<script>
    $("body").on("click", ".ajax-tab", function (event) {
        event.preventDefault();

        $('.project-menu .p-sub-menu').removeClass('active');
        $(this).addClass('active');

        const requestUrl = this.href;
       
        $.easyAjax({
            url: requestUrl,
            blockUI: true,
            container: ".content-wrapper",
            historyPush: true,
            success: function (response) {
                if (response.status === "success") {
                    $('.content-wrapper').html(response.html);
                    init('.content-wrapper');
                }
            }
        });
    });

    $("#search-setting-menu").on("keyup", function () {
        var value = this.value.toLowerCase().trim();
        $("#settingsMenu li").show().filter(function () {
            return $(this).text().toLowerCase().trim().indexOf(value) == -1;
        }).hide();
    });

    document.querySelector('#settingsMenu .active').scrollIntoView()

</script>
