<?php

/*<!--
 * filename, brief description, date of creation, by whom
 * @copyright (C) 2005-2010 University of Sydney Digital Innovation Unit.
 * @link: http://HeuristScholar.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Heurist academic knowledge management system
 * @todo
 -->*/

?>

<?php
	/*<!-- relationships.php

	Copyright 2005 - 2010 University of Sydney Digital Innovation Unit
	This file is part of the Heurist academic knowledge management system (http://HeuristScholar.org)
	mailto:info@heuristscholar.org

	Concept and direction: Ian Johnson.
	Developers: Tom Murtagh, Kim Jackson, Steve White, Steven Hayes,
				Maria Shvedova, Artem Osmakov, Maxim Nikitin.
	Design and advice: Andrew Wilson, Ireneusz Golka, Martin King.

	Heurist is free software; you can redistribute it and/or modify it under the terms of the
	GNU General Public License as published by the Free Software Foundation; either version 3
	of the License, or (at your option) any later version.

	Heurist is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
	even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License along with this program.
	If not, see <http://www.gnu.org/licenses/>
	or write to the Free Software Foundation,Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

	-->*/


/*
$inverses = array();
$inverses['Causes'] = 'IsCausedBy';
$inverses['CollaboratesWith'] = 'CollaboratesWith';
$inverses['CooperatesWith'] = 'CooperatesWith';
$inverses['Funds'] = 'IsFundedBy';
$inverses['HasAssociateDirector'] = 'IsAssociateDirectorOf';
$inverses['HasAssociatePartner'] = 'IsAssociatePartnerIn';
$inverses['HasAuthor'] = 'IsAuthorOf';
$inverses['HasCoAuthor'] = 'IsCoAuthorOf';
$inverses['HasCoConvenor'] = 'IsCoConvenerOf';
$inverses['HasConvenor'] = 'IsConvenerOf';
$inverses['HasDirector'] = 'IsDirectorOf';
$inverses['HasHost'] = 'IsHostOf';
$inverses['HasManager'] = 'IsManagerOf';
$inverses['HasMember'] = 'IsMemberOf';
$inverses['HasNodeDirector'] = 'IsNodeDirectorOf';
$inverses['HasPartner'] = 'IsPartnerIn';
$inverses['HasPhotograph'] = 'IsPhotographOf';
$inverses['HasSpeaker'] = 'IsSpeakerAt';
$inverses['HasSubNode'] = 'IsSubNodeOf';
$inverses['IsOwnedBy'] = 'Owns';
$inverses['IsParentOf'] = 'IsPartOf';
$inverses['IsReferencedBy'] = 'References';
$inverses['IsRelatedTo'] = 'IsRelatedTo';
$inverses['IsSameAs'] = 'IsSameAs';
$inverses['IsSimilarTo'] = 'IsSimilarTo';
$inverses['IsUsedBy'] = 'Uses';
*/

$ranks = array();
$ranks['IsAssociateDirectorOf'] = 3;
$ranks['IsAssociatePartnerIn'] = 3;
$ranks['IsDirectorOf'] = 3;
$ranks['IsManagerOf'] = 3;
$ranks['IsMemberOf'] = 3;
$ranks['IsNodeDirectorOf'] = 3;

$ranks['IsFundedBy'] = 2;
$ranks['IsHostOf'] = 2;
$ranks['IsPartOf'] = 2;

$ranks['IsSpeakerAt'] = 1;
$ranks['IsSubNodeOf'] = 1;

/*
$ranks['Causes'] = 0;
$ranks['CollaboratesWith'] = 0;
$ranks['CooperatesWith'] = 0;
$ranks['Funds'] = 0;
$ranks['HasAssociateDirector'] = 0;
$ranks['HasAssociatePartner'] = 0;
$ranks['HasAuthor'] = 0;
$ranks['HasCoAuthor'] = 0;
$ranks['HasCoConvenor'] = 0;
$ranks['HasConvenor'] = 0;
$ranks['HasDirector'] = 0;
$ranks['HasHost'] = 0;
$ranks['HasManager'] = 0;
$ranks['HasMember'] = 0;
$ranks['HasNodeDirector'] = 0;
$ranks['HasPartner'] = 0;
$ranks['HasSpeaker'] = 0;
$ranks['HasSubNode'] = 0;
$ranks['IsAssociateDirectorOf'] = 0;
$ranks['IsAssociatePartnerIn'] = 0;
$ranks['IsAuthorOf'] = 0;
$ranks['IsCausedBy'] = 0;
$ranks['IsCoAuthorOf'] = 0;
$ranks['IsCoConvenerOf'] = 0;
$ranks['IsConvenerOf'] = 0;
$ranks['IsDirectorOf'] = 0;
$ranks['IsFundedBy'] = 0;
$ranks['IsHostOf'] = 0;
$ranks['IsManagerOf'] = 0;
$ranks['IsMemberOf'] = 0;
$ranks['IsNodeDirectorOf'] = 0;
$ranks['IsOwnedBy'] = 0;
$ranks['IsParentOf'] = 0;
$ranks['IsPartnerIn'] = 0;
$ranks['IsPartOf'] = 0;
$ranks['IsReferencedBy'] = 0;
$ranks['IsRelatedTo'] = 0;
$ranks['IsSameAs'] = 0;
$ranks['IsSimilarTo'] = 0;
$ranks['IsSpeakerAt'] = 0;
$ranks['IsSubNodeOf'] = 0;
$ranks['IsUsedBy'] = 0;
$ranks['Owns'] = 0;
$ranks['References'] = 0;
$ranks['Uses'] = 0;
*/

function reltype_inverse ($reltype) {	//saw Enum change - find inverse as an id instead of a string
	global $inverses;
	if (! $inverses) {
//		$inverses = mysql__select_assoc("defTerms A left join defTerms B on B.trm_ID=A.trm_InverseTermID", "A.trm_Label", "B.trm_Label", "A.rdl_rdt_id=200 and A.trm_Label is not null");
		$inverses = mysql__select_assoc("defTerms A left join defTerms B on B.trm_ID=A.trm_InverseTermID", "A.trm_ID", "B.trm_ID", "A.trm_VocabID=1 and A.trm_Label is not null and B.trm_Label is not null");
	}

	$inverse = @$inverses[$reltype];
	if (!$inverse)
		$inverse = array_search($reltype, $inverses);
	if (!$inverse)
		$inverse = 'Inverse of '.$reltype;

	return $inverse;
}

function reltype_rank ($reltype) {
	global $ranks;
	$rank = $ranks[$reltype];
	if ($rank)
		return $rank;
	else
		return 0;
}


function fetch_relation_details($rec_id, $i_am_primary) {
	/* Raid recDetails for the given link resource and extract all the necessary values */

	$res = mysql_query('select * from recDetails where dtl_RecID = ' . $rec_id);
	$bd = array(
		'ID' => $rec_id
	);
	while ($row = mysql_fetch_assoc($res)) {
		switch ($row['dtl_DetailTypeID']) {
		    case 200:	//saw Enum change - added RelationValue for UI
			if ($i_am_primary) {
				$bd['RelationType'] = $row['dtl_Value'];
			}else{
				$bd['RelationType'] = reltype_inverse($row['dtl_Value']);
			}
			$relval = mysql_fetch_assoc(mysql_query('select trm_Label,trm_VocabID from defTerms where trm_ID = ' .  intval($bd['RelationType'])));
			$bd['RelationValue'] = $relval['trm_Label'];
			$bd['VocabularyID'] = $relval['trm_VocabID'];
			break;

		    case 199:	// linked resource
			if (! $i_am_primary) break;
			$r = mysql_query('select rec_ID, rec_Title, rec_RecTypeID, rec_URL from Records where rec_ID = ' . intval($row['dtl_Value']));
			$bd['OtherResource'] = mysql_fetch_assoc($r);
			break;

		    case 202:
			if ($i_am_primary) break;
			$r = mysql_query('select rec_ID, rec_Title, rec_RecTypeID, rec_URL from Records where rec_ID = ' . intval($row['dtl_Value']));
			$bd['OtherResource'] = mysql_fetch_assoc($r);
			break;

		    case 638:
			$r = mysql_query('select rec_ID, rec_Title, rec_RecTypeID, rec_URL from Records where rec_ID = ' . intval($row['dtl_Value']));
			$bd['InterpResource'] = mysql_fetch_assoc($r);
			break;

		    case 201:
			$bd['Notes'] = $row['dtl_Value'];
			break;

		    case 160:
			$bd['Title'] = $row['dtl_Value'];
			break;

		    case 177:
			$bd['StartDate'] = $row['dtl_Value'];
			break;

		    case 178:
			$bd['EndDate'] = $row['dtl_Value'];
			break;
		}
	}

	return $bd;
}


function getAllRelatedRecords($rec_id, $relnBibID=0) {
	if (! $rec_id) return null;
	$query = "select LINK.dtl_DetailTypeID as type, DETAILS.*, DBIB.rec_Title as title, DBIB.rec_RecTypeID as rt, DBIB.rec_URL as url
from recDetails LINK left join Records LBIB on LBIB.rec_ID=LINK.dtl_RecID, recDetails DETAILS left join Records DBIB on DBIB.rec_ID=DETAILS.dtl_Value and DETAILS.dtl_DetailTypeID in (202, 199, 158)
where ((LINK.dtl_DetailTypeID in (202, 199) and LBIB.rec_RecTypeID=52) or LINK.dtl_DetailTypeID=158) and LINK.dtl_Value = $rec_id and DETAILS.dtl_RecID = LINK.dtl_RecID";
	if ($relnBibID) $query .= " and DETAILS.dtl_RecID = $relnBibID";

	$query .= " order by LINK.dtl_DetailTypeID desc, DETAILS.dtl_ID";

error_log($query);
	$res = mysql_query($query);	/* primary resources first, then non-primary, then authors */

	$relations = array();
	while ($bd = mysql_fetch_assoc($res)) {
		$rec_id = $bd["dtl_RecID"];
		$i_am_primary = ($bd["type"] == 202);
		if (! array_key_exists($rec_id, $relations))
			$relations[$rec_id] = array();

		if (! array_key_exists("Type", $relations[$rec_id])) {
			if ($bd["type"] == 202) {
				$relations[$rec_id]["Type"] = "Primary";
			} else if ($bd["type"] == 199) {
				$relations[$rec_id]["Type"] = "Non-primary";
			} else {
				$relations[$rec_id]["Type"] = "Author";
			}
		}
		if (! array_key_exists("bibID", $relations[$rec_id])) {
			$relations[$rec_id]["bibID"] = $rec_id;
		}

		switch ($bd["dtl_DetailTypeID"]) {
		case 200:	//saw Enum change - nothing to do since dtl_Value is an id and inverse returns an id
			$relations[$rec_id]["RelationType"] = $i_am_primary? $bd["dtl_Value"] : reltype_inverse($bd["dtl_Value"]);
			$relval = mysql_fetch_assoc(mysql_query('select trm_Label from defTerms where trm_ID = ' .  intval($relations[$rec_id]["RelationType"])));
			$relations[$rec_id]['RelationValue'] = $relval['trm_Label'];
			break;

		case 199:
			if ($i_am_primary)
				$relations[$rec_id]["OtherResource"] = array(
					"Title" => $bd["title"], "Rectype" => $bd["rt"], "URL" => $bd["url"], "bibID" => $bd["dtl_Value"]
				);
			break;

		case 202:
			if (! $i_am_primary)
				$relations[$rec_id]["OtherResource"] = array(
					"Title" => $bd["title"], "Rectype" => $bd["rt"], "URL" => $bd["url"], "bibID" => $bd["dtl_Value"]
				);
			break;

		case 638:
			$relations[$rec_id]["InterpResource"] = array(
				"Title" => $bd["title"], "Rectype" => $bd["rt"], "URL" => $bd["url"], "bibID" => $bd["dtl_Value"]);
			break;

		case 201:
			$relations[$rec_id]["Notes"] = $bd["dtl_Value"];
			break;

		case 160:
			$relations[$rec_id]["Title"] = $bd["dtl_Value"];
			break;

		case 177:
			$relations[$rec_id]["StartDate"] = $bd["dtl_Value"];
			break;

		case 178:
			$relations[$rec_id]["EndDate"] = $bd["dtl_Value"];
			break;
		}
	}

	return $relations;
}

?>
