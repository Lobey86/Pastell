<?php
require_once(dirname(__FILE__)."/../init.php");
$functions_list = $objectInstancier->APIDefinition->getSoapFunctions();

header("Content-type: text/xml");

$namespace= "http://pastell.sigmalis.com/service/1.1" ;
$location= SITE_BASE . "api/soap-service.php";


echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<wsdl:definitions 
	xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" 
	xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
	
	xmlns:tns="<?php echo $namespace ?>" 
	xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" 
	xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" 
	xmlns:http="http://www.w3.org/2003/05/soap/bindings/HTTP/"  
	xmlns:xop="http://www.w3.org/2004/08/xop/include"
	xmlns:wsp="http://schemas.xmlsoap.org/ws/2002/12/policy" 
	xmlns:wsam=" http://www.w3.org/2007/05/addressing/metadata"
	targetNamespace="<?php echo $namespace ?>" 	
	>
	<wsdl:types>
		<xsd:schema elementFormDefault="qualified" targetNamespace="<?php echo $namespace ?>" >
			<xsd:import namespace="http://www.w3.org/2004/08/xop/include" schemaLocation="http://www.w3.org/2004/08/xop/include"/>		
		</xsd:schema>
	</wsdl:types>
	
	<?php foreach($functions_list as $function_name => $function_properties) : ?>
	<wsdl:message name="<?php hecho($function_properties['soap-name'])?>">
		<?php foreach($function_properties[APIDefinition::KEY_INPUT] as $name => $value): ?>
			<wsdl:part name="<?php hecho($name)?>" element="xsd:anyType"/>
		<?php endforeach;?>
	</wsdl:message>	
	<wsdl:message name="<?php hecho($function_properties['soap-name'])?>Response">
	<?php if ($function_properties[APIDefinition::KEY_OUTPUT] ) : ?>
		<wsdl:part name="return" element="xsd:anyType"/>
	<?php endif;?>
	</wsdl:message>
	<?php endforeach;?>
	
	<wsdl:portType name="PastellSoap">
	<?php foreach($functions_list as $function_name => $function_properties) : ?>
		<wsdl:operation name="<?php hecho($function_properties['soap-name'])?>">
			<wsdl:input message="tns:<?php hecho($function_properties['soap-name'])?>"/>
			<wsdl:output message="tns:<?php hecho($function_properties['soap-name'])?>Response"/>
		</wsdl:operation>	
	<?php endforeach;?>
	</wsdl:portType>
	
	<wsdl:binding name="PastellSoap" type="tns:PastellSoap">		
		<soap:binding transport="http://schemas.xmlsoap.org/soap/http" style="rpc"/>
		<?php foreach($functions_list as $function_name => $function_properties) : ?>
			<wsdl:operation name="<?php hecho($function_properties['soap-name'])?>">
				<soap:operation soapAction="<?php echo $namespace?>/<?php hecho($function_properties['soap-name'])?>" />
				<wsdl:input>
					<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="<?php echo $namespace?>"/>
				</wsdl:input>
				<wsdl:output>
					<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="<?php echo $namespace?>" parts="return"/>
				</wsdl:output>
			</wsdl:operation>
		<?php endforeach;?>		
	</wsdl:binding>
	
	<wsdl:service name="Pastell">
		<wsdl:port name="PastellSoap" binding="tns:PastellSoap">
			<soap:address location="<?php echo $location ?>"/>
		</wsdl:port>
	</wsdl:service>
</wsdl:definitions>
