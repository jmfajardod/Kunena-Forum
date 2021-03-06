/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.dialog.add( 'pollsDialog', function( editor ) {
	var options = null;
	var nboptionsmax = jQuery('#nb_options_allowed').val();

	function createNewOptionField() {
		options++;
		var paragraph = new CKEDITOR.dom.element( 'p' );
		paragraph.setStyle( 'margin-top', '5px' );
		var label = new CKEDITOR.dom.element( 'label' );
		label.appendText(Joomla.JText._('COM_KUNENA_POLL_OPTION_NAME')+ ' ' + options + ' ');
		label.setAttribute('id', 'labeloption' + options);
		paragraph.append( label );
		var inputField = new CKEDITOR.dom.element( 'input' );
		inputField.addClass( 'cke_dialog_ui_input_text' );
		inputField.setAttribute('id', 'field_option' + options);
		inputField.setAttribute('name', 'polloptionsID[new' + options + ']' );
		inputField.setAttribute('type', 'text');
		inputField.setAttribute('maxLength', 100);
		paragraph.append( inputField );

		CKEDITOR.document.getById( 'dynamicContent' ).append( paragraph );
	}

	return {
		title: Joomla.JText._('COM_KUNENA_EDITOR_DIALOG_POLLS_PROPERTIES'),
		minWidth: 400,
		minHeight: 200,
		contents: 
		[
			{
				id: 'tab-basic',
				label: Joomla.JText._('COM_KUNENA_EDITOR_DIALOG_BASIC_SETTINGS'),
				elements: 
				[
					{
						type: 'text',
						id: 'polltitle',
						label: 'Poll title',
						default: ''
					},
					{
						type: 'button',
						id: 'polladdoption',
						label: Joomla.JText._('COM_KUNENA_POLL_ADD_POLL_OPTION'),
						title: Joomla.JText._('COM_KUNENA_POLL_ADD_POLL_OPTION'),
						onClick: function() {
							// this = CKEDITOR.ui.dialog.button
							if (!nboptionsmax || (options < nboptionsmax && options >= 2)) {
								createNewOptionField();
							}
							else if (!nboptionsmax || options < 2) {
								createNewOptionField();
								createNewOptionField();
							}
							else {
								// TODO :  Hide button add
								
								console.log('max options reach ');
							}
						}
					},
					{
						type: 'button',
						id: 'pollremoveoption',
						label: Joomla.JText._('COM_KUNENA_POLL_REMOVE_POLL_OPTION'),
						title: Joomla.JText._('COM_KUNENA_POLL_REMOVE_POLL_OPTION'),
						onClick: function() {
							// this = CKEDITOR.ui.dialog.button
							jQuery('#field_option' + options).remove();
							jQuery('#labeloption' + options).remove();
							options--;
							
							// TODO : show button hide if it was hidden
						}
					},
					{
						type: 'text',
						id: 'polllifespan',
						label: Joomla.JText._('COM_KUNENA_POLL_TIME_TO_LIVE'),
						default: ''
					},
					// Add HTML container for dynamic content
					{
						id : 'divdynamiccontent',
						type: 'html',
						html: '<div id="dynamicContent"></div>',
						setup: function(selectedTable) {

						},
						commit: function(data) {

						}
					}
				]
			}
		],
		onOk: function() {

		}
	};
});