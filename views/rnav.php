<div id="toolbar-all">
	<a href="?display=announcement" class="btn btn-default"><i class="fa fa-list"></i> <?php echo _("List Announcements")?></a>
	<a href="?display=announcement&amp;view=form" class="btn btn-default"><i class="fa fa-plus"></i> <?php echo _("Add Announcements")?></a>
</div>
 <table id="destgrid-side" data-url="ajax.php?module=customappsreg&amp;command=getJSON&amp;jdata=destgrid" data-cache="false" data-toolbar="#toolbar-all" data-toggle="table" data-search="true" class="table">
    <thead>
        <tr>
            <th data-field="description" data-sortable="true"><?php echo _("Destinations")?></th>
        </tr>
    </thead>
</table>
