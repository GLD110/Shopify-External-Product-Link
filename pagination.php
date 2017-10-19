<?php
function paginate_three($reload, $page, $tpages, $adjacents) {
	
	$prevlabel = "<";
	$nextlabel = ">";
	
	$out = "<ul class=\"pagination\">";
	
	// previous
	if($page==1) {
		$out.= "<li>" . $prevlabel . "</li>\n";
	}
	elseif($page==2) {
		$out.= "<li><a href=\"" . $reload . "\">" . $prevlabel . "</a></li>\n";
	}
	else {
		$out.= "<li><a href=\"" . $reload . "page=" . ($page-1) . "\">" . $prevlabel . "</a></li>\n";
	}
	
	// first
	if($page>($adjacents+1)) {
		$out.= "<li><a href=\"" . $reload . "\">1</a></li>\n";
	}
	
	// interval
	if($page>($adjacents+2)) {
		$out.= "...\n";
	}
	
	// pages
	$pmin = ($page>$adjacents) ? ($page-$adjacents) : 1;
	$pmax = ($page<($tpages-$adjacents)) ? ($page+$adjacents) : $tpages;
	for($i=$pmin; $i<=$pmax; $i++) {
		if($i==$page) {
			$out.= "<li class=\"current\">" . $i . "</li>\n";
		}
		elseif($i==1) {
			$out.= "<li><a href=\"" . $reload . "\">" . $i . "</a></li>\n";
		}
		else {
			$out.= "<li><a href=\"" . $reload . "page=" . $i . "\">" . $i . "</a></li>\n";
		}
	}
	
	// interval
	if($page<($tpages-$adjacents-1)) {
		$out.= "...\n";
	}
	
	// last
	if($page<($tpages-$adjacents)) {
		$out.= "<li><a href=\"" . $reload . "page=" . $tpages . "\">" . $tpages . "</a</li>\n";
	}
	
	// next
	if($page<$tpages) {
		$out.= "<li><a href=\"" . $reload . "page=" . ($page+1) . "\">" . $nextlabel . "</a></li>\n";
	}
	else {
		$out.= "<li>" . $nextlabel . "</li>\n";
	}
	
	$out.= "</ul>";
	
	return $out;
}
?>