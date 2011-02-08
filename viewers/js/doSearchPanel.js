/*
 * filename, brief description, date of creation, by whom
 * @copyright (C) 2005-2010 University of Sydney Digital Innovation Unit.
 * @link: http://HeuristScholar.org
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Heurist academic knowledge management system
 * @todo
 */


var resultsDiv;

function search(query) {
	if (! query) return;
	var loader = new HLoader(
		function(s,r) {
			displayResults(s,r);
		},
		function(s,e) {
			alert("load failed: " + e);
		}
	);
	loadAllRecords(query, null, loader);
	showSearch(query);

}

function loadAllRecords(query, options, loader) {
		var records = [];
		var baseSearch = new HSearch(query, options);
		var bulkLoader = new HLoader(
			function(s, r) {	// onload
				records.push.apply(records, r);
				if (r.length < 100) {
					// we've loaded all the records: invoke the original loader's onload
					document.getElementById('loading-msg').innerHTML = '<b>Loaded ' + records.length + ' records </b>';
					loader.onload(baseSearch, records);
				}
				else { // more records to retrieve
					document.getElementById('loading-msg').innerHTML = '<b>Loaded ' + records.length + ' records so far ...</b>';

					//  do a search with an offset specified for retrieving the next page of records
					var search = new HSearch(query + " offset:"+records.length, options);
					HeuristScholarDB.loadRecords(search, bulkLoader);
				}
			},
			loader.onerror
		);
		HeuristScholarDB.loadRecords(baseSearch, bulkLoader);
}

function showSearch(query) {
	// hide footnotes
	document.getElementById("footnotes").style.display = "none";
	document.getElementById("page").style.bottom = "0px";

	// turn off any highlighting
	if (window.highlightElem) {
		highlightElem("inline-annotation", null);
		highlightElem("annotation-link", null);
	}

	// hide page-inner and show results div
	var pageInner = document.getElementById("page-inner");
	pageInner.style.display = "none";
	if (resultsDiv) {
		resultsDiv.innerHTML = "";
		resultsDiv.style.display = "block";
	} else {
		resultsDiv = pageInner.parentNode.appendChild(document.createElement("div"));
		resultsDiv.id = "results-div";
	}

	resultsDiv.innerHTML += "<a style=\"float: right;\" href=# onclick=\"hideResults(); return false;\">Return to previous view</a>";
	resultsDiv.innerHTML += "<h2>Search results for query \"" + query + "\"</h2>";
	resultsDiv.innerHTML += "<p id=loading-msg>Loading...</p>";
}

function displayResults(s,r) {
	var l = document.getElementById("loading-msg");
	l.parentNode.removeChild(l);

	var innerHTML = "";	//FIXME:  need to change this to be generic to the install
	var thisInstancePath = (HAPI && HAPI.HeuristBaseURL ? HAPI.HeuristBaseURL : (window.HeuristBaseURL ? window.HeuristBaseURL : (window.opener.HeuristBaseURL? window.opener.HeuristBaseURL: "../../")));
	for (var i = 0; i < r.length; i++) {
		if (r[i].getRecordType()){
			innerHTML += "<img src=\""+ thisInstancePath + "common/images/rectype-icons/" + r[i].getRecordType().getID() + ".png\"/>";
			innerHTML += " <a href='" + thisInstancePath + "records/view/viewRecord.php?bib_id=" + r[i].getID() + "' target=\"_blank\">" + r[i].getTitle() + "</a><br/>";
		}
	}

	if (innerHTML.length) {
		resultsDiv.innerHTML += innerHTML;
	} else {
		resultsDiv.innerHTML += "<p>No matching records</p>";
	}
}

function hideResults() {
	resultsDiv.style.display = "none";
	document.getElementById("page-inner").style.display = "block";
	if (window.higlightOnLoad) highlightOnLoad();
}

