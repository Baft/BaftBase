FormManager is a pattern that collect some html code together to work in special way
so a FormManager is a Core module that made to make easy works and made under the DomDocument library
so can not be part of the library framework , may be Core module or suppelment library 
BUT BUT FormCreator and Basical Form functionality that did not dependent to System Design like mvc , hmvc , ...
be exist to Framework

/*############## MAKING #################*/
an instance of FORM class have instance of Form_Element Class

$form=new form();

$form->add_element("input");  add_element function return an Form_element class instance and add one form_element instance to 
elements list of form , this function use $html=new Dom()->make("<input>"); in own body to make an element and save it on FromElementssArray

$form->add_element("input")->set_attribute("id","xx")->set_attribute( array("class" => "yyy" , "name" => "hhh" , ...) ); set attribute of form_element class instance
and can work in two mode , name-value , or arraye of name-value , this function is facade pattern of DomAttribute class of the DomDocument class , but because of FACADE 
model and is part of FormElement class under FormManager

$form->add_element("input")->filter($pattern , $message )
$form->add_element("input")->filter("string" , $message );
$form->add_element("input")->filter("number" , $message );
$form->add_element("input")->filter("mail" , $message ); define valid type of the form elements , that work in two mode , give up a pattern to check with it , or give up a predefined type

/*############## VIEW #################*/
$form->get_element("input#gg");
$form->get_element("input.gg");
$form->get_element("input[attribName:attribValue]"); in the view , by this functin get an/array elements that match selector

$form->get_elements(); get all form element

$form->



/*############## CONTROLLING #################*/