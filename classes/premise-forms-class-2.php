<?php
/**
 * Tribus Form Class
 *
 * This class allows us to easily build form elements using aparameters within a PHP Array.
 *
 * @package Tribus Framework
 * @subpackage Forms Class
 */





/**
* 
*/
class PremiseField {


	/**
	 * holds initial agrumnets passed to the class
	 * 
	 * @var array
	 */
	protected $args = array();


	
	

	/**
	 * Defaults for each field
	 * 
	 * @var array
	 */
	protected $deaults = array(
		'type' 	  		  => 'text',		//i.e. textarea, select, checkbox, file
		'name' 	  		  => '',
		'id' 	  		  => '',
		'label' 	  	  => '',
		'placeholder' 	  => '',  			//also used as select default option if not empty
		'tooltip' 	  	  => '',  			//displays balloon style tooltip
		'value' 	  	  => '',  			//value from database
		'value_att' 	  => '',  			//Used for checkboxes and radio fields. if this is equal to 'value' the field will be checked
		'class' 	  	  => '',  			//custom class for easy styling
		'insert_icon'	  => '', 			//insert a fontawesome icon
		'options'		  => array(),		//holds different options depending on the type of field
		'attribute' 	  => '',			//Additional html attributes to add to element i.e. onchange="premiseSelectBackground()"
	);






	/**
	 * holds our field
	 * 
	 * @var array
	 */
	protected $field = array();




	

	/**
	 * will hold our button markup to our object assigned in prepare_field()
	 * 
	 * @var string
	 */
	protected $btn_upload_file;
	protected $btn_remove_file;
	protected $btn_choose_icon;
	protected $btn_remove_icon;






	/**
	 * Holds the html for this field(s)
	 * 
	 * @var string
	 */
	public $html = '';






	/**
	 * construct our object
	 * 
	 * @param array $args array holding one or more fields
	 */
	function __construct( $args ) {
		
		if( !empty( $args ) && is_array( $args ) )
			$this->args = $args;

		$this->field_init();

	}





	/**
	 * begin processing the field
	 */
	protected function field_init() {

		/**
		 * 
		 */
		$this->field = wp_parse_args( $this->args, $this->defaults );

		$this->prepare_field();

		$this->build_field();
				
	}






	/**
	 * This function builds our field and saves the html markup for it
	 */
	protected function build_field() {

		switch( $this->field['type'] ) {
			case 'select':
			case 'wp_dropdown_pages':
				$html .= $this->select_field();
				break;

			case 'textarea':
				$html .= $this->textarea();
				break;

			case 'checkbox':
				$html .= $this->checkbox();
				break;

			case 'radio':
				$html .= $this->radio();
				break;

			default:
				$html .= $this->input_field();
				break;
		}

		$this->html .= $html;

	}







	protected function input_field() {

		$field  = '<input type="'. $this->field['type'] .'"';

		$field .= !empty( $this->field['name'] ) 		? 'name="'. $this->field['name'] .'"' 	: '';
		$field .= !empty( $this->field['id'] ) 			? 'id="'. $this->field['id'] .'"' 		: '';
		$field .= !empty( $this->field['value'] ) 		? 'value="'. $this->field['value'] .'"' : '';
		$field .= !empty( $this->field_class )			? 'class="'. $this->field_class .'"'	: '';
		$field .= !empty( $this->field['attribute'] ) 	? $this->field['attribute'] 			: '';
		
		$field .= '>';

		/**
		 * add buttons if file or fa-icon field
		 */
		switch( $this->field['type'] ) {
			case 'file':
				$field .= $this->btn_upload_file;
				$field .= $this->btn_remove_file;
				break;

			case 'fa-icon':
				$field .= $this->btn_choose_icon;
				$field .= $this->btn_remove_icon;
				break;
		}

		return $field;

	}








	protected function textarea() {
		
		$field = '<textarea ';

		$field .= !empty( $this->field['name'] ) ? 'name="'.$this->field['name'].'"' : '';
		$field .= !empty( $this->field['id'] ) ? 'id="'.$this->field['id'].'"' : '';
		$field .= !empty( $this->field['placeholder'] ) ? 'placeholder="'.$this->field['placeholder'].'"' : '';
		$field .= !empty( $this->field['attribute'] ) ? $this->field['attribute'] : '';

		$field .= '>'.$this->field['value'].'</textarea>';

		return $field;
	}







	protected function checkbox() {
		
		$field  = '<input type="'. $this->field['type'] .'"';
		
		$field .= !empty( $this->field['name'] ) 		? 'name="'. $this->field['name'] .'"' 		: '';
		$field .= !empty( $this->field['id'] ) 			? 'id="'. $this->field['id'] .'"' 			: '';
		$field .= !empty( $this->field['value_att'] ) 	? 'value="'. $this->field['value_att'] .'"' : '';
		$field .= !empty( $this->field['class'] ) 		? 'class="'. $this->field['class'] .'"' 	: '';
		$field .= !empty( $this->field['attribute'] ) 	? $this->field['attribute'] 				: '';

		$field .= checked( $this->field['value'], $this->field['value_att'], false );

		$field .= '>';

		$field .= '<label ';
		$field .= !empty( $this->field['id'] ) 			? 'for="'. $this->field['id'] .'"' 		: '';
		$field .= '>'. $this->options['label'] .'</label>';

		return $field;

	}







	protected function radio() {
		if( !empty( $this->field['options'] ) && is_array( $this->field['options'] ) ) {
			
			foreach ( $this->field['options'] as $radio ) {
				
				$field  = '<input type="'.$this->field['type'].'"';
				
				$field .= !empty( $this->field['attribute'] ) 	? $this->field['attribute'] 		: '';
				$field .= !empty( $this->field['name'] ) 		? 'name="'.$this->field['name'].'"' : '';
				$field .= !empty( $radio['id'] ) 				? 'id="'.$radio['id'].'"' 			: '';
				$field .= !empty( $radio['value_att'] ) 		? 'value="'.$radio['value_att'].'"' : '';
				
				$field .= checked( $this->field['value'], $radio['value_att'], false );

				$field .= '>';

				$field .= '<label ';
				$field .= !empty( $radio['id'] ) ? 'for="'.$radio['id'].'">' : '';
				$field .= $radio['label'].'</label>';

			}

		}
	}






	protected function select_field() {
		
		if( 'wp_dropdown_pages' == $this->field['type'] ) {
			$field = $this->do_wp_dropdown_pages();
		}
		else {
			$field  = '<select '.$this->field['attribute'].' name="'.$this->field['name'].'" id="'.$this->field['id'].'">';
			$field .= !empty( $this->field['placeholder'] ) ? '<option>'.$this->field['placeholder'].'</option>' : '';
			$field .= $this->select_options();
			$field .= '</select>';
		}

		return $field;
	}







	protected function select_options() {
		
		$options = '';

		if( is_array( $this->field['value'] ) ) {
			foreach ( $this->field['options'] as $key => $value ) {
				$options .= '<option  value="'.$value.'"';
				$options .= (is_array( $this->field['value'] ) && in_array( $value, $this->field['value'] ) ) ? 'selected' : '';
				$options .= '>'.$key.'</option>';
			}
		}
		else {
			foreach ($this->field['options'] as $key => $value) {
				$options .= '<option  value="'.$value.'"';
				$options .= selected( $this->field['value'], $value, false );
				$options .= '>'.$key.'</option>';
			}	
		}

		return $options;
	}








	protected function do_wp_dropdown_pages() {
		
		$new_defaults = array(  
			'depth' 				=> 0, 
			'child_of' 				=> 0,
    		'selected' 				=> $this->field['value'], 
    		'name' 					=> $this->field['name'],
    		'id' 					=> $this->field['id'],
    		'show_option_none' 		=> $this->field['placeholder'], 
    		'show_option_no_change' => '',
    		'option_none_value' 	=> '', 
    	);
		
		$this->field = wp_parse_args( $this->field, $new_defaults );

		/**
		 * Make sure this never gets echoed.
		 */
		$this->field['echo'] = 0;
		
		return wp_dropdown_pages( $this->field );
	}






	/**
	 * Prepare our field. This function assigns the values to the 
	 * class properties needed to build a particular field
	 */
	protected function prepare_field() {

		/**
		 * Set the field['type'] value
		 */
		switch( $this->field['type'] ) {
			case 'wp_dropdown_pages':
				$this->field['type'] = 'select';
				break;


			case 'minicolors':
				$this->field['type'] = 'text';
				$this->field_class = 'premise-minicolors';
				break;

			case 'file':
				$this->field['type'] = 'text';
				$this->field_class = 'premise-file-url';
				$this->btn_upload_file = '<a class="premise-btn-upload" href="javascript:void(0);" onclick="premiseUploadFile(this, '.$multiple.', \''.$preview.'\')"><i class="fa fa-fw fa-upload"></i></a>';
				$this->btn_remove_file = '<a class="premise-btn-remove" href="javascript:void(0);" onclick="premiseRemoveFile(this)"><i class="fa fa-fw fa-times"></i></a>';
				break;

			case 'fa-icon':
				$this->field['type'] = 'text';
				$this->field_class = 'premise-insert-icon';
				$this->btn_choose_icon = '<a href="javascript:;" class="premise-choose-icon" onclick="premiseChooseIcon(this);"><i class="fa fa-fw fa-th"></i></a>';
				$this->btn_remove_icon = '<a href="javascript:;" class="premise-remove-icon" onclick="premiseRemoveIcon(this);"><i class="fa fa-fw fa-times"></i></a>';
				break;

			case 'checkbox':
			case 'radio':
				$this->label  = !empty( $this->field['label'] ) 												? '<p class="label">'.$this->field['label'].'</p>' 							: '';
				$this->label .= ( !empty( $this->field['label'] ) && !empty( $this->field['tooltip'] ) ) 		? '<span class="tooltip"><i>'.$this->field['tooltip'].'</i></span>' 		: '';
				break;

			default :
				$this->label  = !empty( $this->field['label'] ) 												? '<label for="'.$this->field['id'].'">'.$this->field['label'].'</label>' 	: '';
				$this->label .= ( !empty( $this->field['label'] ) && !empty( $this->field['tooltip'] ) ) 		? '<span class="tooltip"><i>'.$this->field['tooltip'].'</i></span>' 		: '';
				break;
		}

	}


}
?>