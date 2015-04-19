<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Report</title>
<style>
/* Style copi� inspired by http://coding.smashingmagazine.com/2008/08/13/top-10-css-table-designs/ */
::selection {
  background: rgb(51,51,153);
  color: rgb(252,252,252);
}
body {
  width: 95%;
  margin-top: 4em;
  margin-bottom: 6em;
  font-family: 'Lucida Grande', 'Helvetica Neue', sans-serif;
  font-size: 12px;
  font-style: normal;
  font-weight: normal;
  text-align: left;
  vertical-align: baseline;
  color: rgb(51,51,153);
  background-color: rgb(252,252,252);
}
figure {
  width: 100%;
  margin: 0px;
  padding: 0px;
}
h1, h2, h3, h4, h5, h6 {
  text-align: center;
}
pre {
  font-family: 'Lucida Grande', 'Helvetica Neue', sans-serif;
  color: rgb(102,102,153);
  line-height:25px;
  margin:0px;
}
table {
  display: table;
  line-height: 22px;
  width:80%;
  margin: auto;
  -webkit-border-horizontal-spacing: 0px;
  -webkit-border-vertical-spacing: 0px;
}
thead {
  display: table-header-group;
  height: 43px;
  line-height: 22px;
  margin: 0px;
  padding: 0px;
  color: rgb(51,51,153);
  background-color: rgba(0,0,0,0);
  outline-color: rgb(51,51,51);
  outline-style: none;
  outline-width: 0px;
}
th {
  display: table-cell;
  font-size: 14px;
  margin: 0px;
  padding: 10px 8px;
  font-weight: normal;
  color: rgb(51,51,153);
	border-bottom: 2px solid rgb(102,120,177);
  border-collapse: collapse;
}
tr {
  display: table-row;
  margin: 0px;
  padding: 0px;
}
tr:nth-child(even) {
 /* background-color: rgb(250, 250, 255);*/
}
tr:hover td {
  color: rgb(0,0,153);
  background-color: rgb(242,243,255);
}

td {
  display: table-cell;
  vertical-align: top;
	padding: 5px 8px;
	color: rgb(102,102,153);
}
.first-posting td {
  border-top: 1px dotted rgb(182,190,216);
  border-collapse: collapse;
}
tr:first-child > td {
  border: none;
}
td.amount {
  white-space: pre;
  text-align: right;
  vertical-align: top;
}
/*svg {
  width: 100%;
}*/
#monthly-expenses .first-period td,
#monthly-expenses-related .first-period td {
	border-top: 2px solid rgb(102,120,177);
  border-collapse: collapse;
}
#monthly-expenses th, #monthly-expenses-related th {
  border-bottom: none;
}
#cash-flow-balance th {
  border-bottom: none;
}
div#monthly-net-worth,
div#weekly-net-worth {
  width:30%;
  margin:auto;
  float:right;
}
.account {
}
.amount {
  font-family: Menlo, Monaco, Courier, monospace;
  width:20%;
  float:left;
  text-align:right;
}
.balance-report table {
  line-height: 16px;
}
.balance-report td {
  vertical-align: top;
  padding-top: 5px;
  padding-bottom: 5px;
	padding-left: 8px;
  padding-right: 8px;
}
.balance-report .account,
.budget-report .account {
  white-space: pre;
}
.balance-report td.amount {
  width: 25%;
  min-width: 130px;
}
.date {
  min-width: 100px;
}
.balance-report td.total,
.budget-report td.total {
  padding-top: 10px;
	border-top: 2px solid rgb(102,120,177);
}
.date {
}
.future {
  font-style: italic;
}
.header-amount, .header-average, .header-balance,
.header-debit, .header-credit, .header-deviation,
.header-net-worth, .header-total,
.header-actual, .header-budgeted,
.header-remaining, .header-used,
.header-cleared, .header-pending {
  text-align: right;
}
.improper {
  color: rgb(153,26,0);
  font-weight: bold;
}
.ledger-command {
  display: none;
}
.neg {
  color: rgb(153,26,0);
}
.payee {
}
.pending .payee {
  color: rgb(153,77,0);
  font-weight: bold;
}
.perc {
  text-align: right;
}
.total {
  font-weight: bold;
  padding-top: 10px;
  border-top: 2px solid rgb(102,120,177);
  width:40%;
  float:left;
  text-align:right;
}
/*border-right: 2px solid #ff0000;*/
.border-account {
	width:20px;
	float:left;
	margin-top:-3px;
	
}

.border-subaccount {
	width:20px;
	float:left;
	margin-top:-3px;
}

.container:hover {
  color: rgb(0,0,153);
  background-color: rgb(242,243,255);
}

#subaccount a {
text-decoration:none;
}

</style>
</head>
<body>
<section class="balance-report">
<?php 
setlocale(LC_ALL, 'da_DK');
?>