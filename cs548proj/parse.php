<?PHP 
	//$url = "http://en.wikipedia.org/w/api.php?action=parse&page=".$_POST["input"]."&format=xml&prop=text&section=0";
	//$fpdemo  =   simplexml_load_file("ACMComputingClassificationSystemSKOSTaxonomy.xml");//"plant_small_catalog.xml"
	//$RDF = file_get_contents(url);
	//echo $RDF;
	//$file = fopen("ACMComputingClassificationSystemSKOSTaxonomy.xml","r+");
	//echo $file;
	//$fpdemo = fopen("ACMComputingClassificationSystemSKOSTaxonomy.xml","r+"); 
    //if ($fpdemo){ 
    // while(!feof($fpdemo)){ 
      //1000读取的字符数 
     // $datademo = fread($fpdemo, 100000); 
     //} 
     //fclose($fpdemo); 
    //} 
    //echo $datademo; 
	
	$RDF = file_get_contents("test.xml","r+");
	//$RDF = str_replace('rdf:', 'rdf_', $RDF);
	//$RDF = str_replace('rdfs:', 'rdfs_', $RDF);
	//$RDF = str_replace('xmlns:', 'xmlns_', $RDF);
	//$RDF = str_replace('skos:', 'skos_', $RDF);
	//$RDF = str_replace('dc:', 'dc_', $RDF);
	echo $RDF;
	/*$XML = simplexml_load_string($RDF);
	echo $XML->skos_Concept[0]->skos_prefLabel;
	$items = $XML->skos_Concept;
	foreach ($items as $item){
		echo $item->skos_prefLabel;
		echo $item->attributes()->rdf_about;
	}
*/
	//echo $fpdemo->PLANT->COMMON;
?>
