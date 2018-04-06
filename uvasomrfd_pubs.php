<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);

/************************************************************************************/
//sort function to order by date in descending order
function cmp($a, $b){
    if ((int)$a->date[0] == (int)$b->date[0]) {
        return 0;
    }
    return ((int)$a->date[0] > (int)$b->date[0]) ? -1 : 1;
}
/************************************************************************************/
function uvasomrfd_get_publications($curvid) {
//$uvaid = $_GET['facultyid'];
//$facpubs = "";
// Get any existing copy of our transient data of faculty publications
if ( false === ( $facpubs = get_transient( 'faculty_pubs_'.$curvid) ) ) {
  // It wasn't there, so regenerate the data and save the transient
	
	//get the xml file
	$xml = simplexml_load_file('/sharedassets/curvita/pubs_load.php');
	//make an array from the data in the file
	$pubs = array();
	foreach($xml->article as $p)
	{
		$pubs[] = $p;
	}
	//if there is no data in the array, return a message to the browser
	if(empty($pubs)) {die('');}
	
	//otherwise, apply the sort function to the array
	usort($pubs, "cmp");

	// $pubs is now sorted by the dates in descending order
	/************************************************************************************/
	
	//display the title of the section and beginning of the list
		$facpubs = '<h4 class="publications" id="'.get_post_meta(get_the_ID(),'wpcf-curv_id',true ).'">Selected Publications</h4>'."\n";
		$facpubs .= '<ul class="publications" id="publications-'.get_post_meta(get_the_ID(),'wpcf-curv_id',true ).'">'."\n";
	
	
	// output publication list
		  foreach($pubs as $articles) {
			$facpubs .= "<li>"."\n";
			foreach($articles->children() as $authors) {
			  foreach($authors->children() as $author) {
				$facpubs .= $author->{'last-name'}." ".$author->{'first-name'};
				if(!empty($author->{'middle-initial'}))
				{ 
				$facpubs .= $author->{'middle-initial'};
				}
				$facpubs .= ", ";
				;
			  }	
		
			}
			$facpubs .= $articles->title.", ";
			$facpubs .= substr($articles->{'date'},0,4)."; ";
			$facpubs .= $articles->publisher.". ";
			$facpubs .= $articles->volume;
			$facpubs .= "(".$articles->issue.") ";
			$facpubs .= $articles->pages.". ";
			$facpubs .= '<a href="http://www.ncbi.nlm.nih.gov/pubmed/'.$articles->externalID.'" target="_blank">PMID: '.$articles->externalID.'</a>';
				if(!empty($articles->pmcid))
			{ 
			$facpubs .= ' | <a href="http://www.ncbi.nlm.nih.gov/pmc/articles/'.$articles->pmcid.'" target="_blank">PMCID: '.$articles->pmcid.'</a>';
			}
			$facpubs .= "</li>"."\n";
			$facpubs .= '<div class="clearfix faculty"></div>'."\n";
	
			}		
			$facpubs .= '</ul>'."\n";
		//set the transient data
			set_transient( 'faculty_pubs_'.$curvid, $facpubs );
			}
		// Otherwise display the tranvient data
		else {
			$facpubs = get_transient( 'faculty_pubs_'.$curvid ) ;
		}
		echo $facpubs;

}
?>