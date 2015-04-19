<?php

	/*ini_set("log_errors" , "1");
	ini_set("error_log" , "/Logs/errors.log.txt");
	ini_set("display_errors" , "1");*/

	$xml=simplexml_load_file("ledger.xml");
	
	define("HTML_FOLDER", "html/");
	define("CSV_FOLDER", "csv/");
	
	// Start create transactions files
	$trans_array = array();
	foreach($xml as $ledger)
	{
		if (count($ledger->transaction) > 0) {
			$trans_array = getTransactionsToArray($ledger->transaction);
			printTransactions($trans_array);
			
		}
	}
	// End create transactions files
	
	// Start create report file (index.html)
	ob_start();
	include ("header.php");  
?>
<script type="text/javascript">
function display(rowno){
	url = document.getElementById('account_name'+rowno).value;
	if (url == "") {
		//alert("No transactions found.");
	} else { 
		var win=window.open(url+".html", 'transaction_page');
		win.focus();  
	}
}

function open_csv(rowno){
	url = document.getElementById('account_name'+rowno).value;
	if (url == "") {
		//alert("No transactions found.");
	} else { 
		var win=window.open("../Debitorer/csv/"+url+".csv");
		win.focus();  
	}
}
</script>

<h1>Report</h1>
<?php

	$rowno = 0; // Use to define row number for identify link to transactions
	$firstfile = "";
	
	foreach($xml as $ledger)
	{
		foreach ($ledger as $elements) {
			$round = 0; //Round count up when there is sub-account
			$num_sub_account = 99;
			echo "<pre>";
			print_subaccount($elements->account, $round, $num_sub_account,null, null, $rowno);
			
			//GET TOTAL
			echo "<div class='container'>";
			foreach ($elements->children() as $total) {
					foreach ($total->children() as $amount) {
						if ($total->getName() == "account-total") {
							echo  "<span class='total' style='".setTextRed($amount->quantity)."'>".money_format('%!n', (float)$amount->quantity). "</span>";
						}
					}
			}
			echo "</div>";
			echo "</pre>";
		}
		echo "<div style='clear:both'></div>";
	}
	
	/*
	* Recursive function for printing sub-account
	*/
	function print_subaccount($accounts, $round, $num_sub_account, $parentname=null, $lastparent=null, $rowno) {
		// Array of colours, use to display line in difference colours in front of account and sub-accounts.
		// This reserve for 8 sub-accounts
		$colors = array ("#ff0000", "#01DF01", "#58ACFA", "#F781F3", "#FFFF00" , "#A4A4A4",  "#B43104",  "#5F04B4",  "#A9BCF5");
		
		// Count number of account's child
		$count = 0;
		foreach ($accounts as $account) {
			if(isset($account->name))$count+=1;
		}
		if($count>1) $round++;
				
		//If only one sub account, display only 1 line
		if ($count == 1) {	
			if (isset($account->children()->account->name)) {
				//Print sub-name
				echo ":".$account->children()->account->name."";
				
				$filename = createTransactionFileName($account->children()->account, $rowno);
				
				//Override url for the link to transaction data when there is sub-account
				echo "<script type=\"text/javascript\">
					var e = document.getElementById('account_name".$rowno."'); e.value='".$filename."'; 
					</script>";
			}

			$rowno = print_subaccount($accounts->children()->account, $round, $num_sub_account,$account->children()->account->name, $parentname, $rowno);
			echo "</div>"; //End div.container
			
			
		} 
		else {
			echo "</div>"; //End div.container
			
			foreach ($accounts as $sub_account) {
				$rowno ++;
				//Calculate block space width
				$width = 20*($round-1);
				
				$num_sub_account = 0;
				foreach ($sub_account->children()->account as $under_sub) {
					$num_sub_account+=1;
				}
				
				// Return when there is only 1 sub-account
				if ($num_sub_account == 0 && $count == 1) return;
				
				if (isset($sub_account->name)) {
					printf("<div id='link_csv".$rowno."' style='float:right'><a onclick='open_csv(%s)'>", $rowno);
					echo '<img style="border:0;float:right" src="csv.png" width="20" height="20">';
					echo "</a></div>";
				}
				
				printf("<a id='link_trans' onclick='display(%s)'>", $rowno);
				echo "<div id='subaccount'>"; 
				echo "<div id ='account_row".$rowno."' class='container'>"; //Start div.container
				
				// **Print account quantity**
				foreach ($sub_account->children() as $total) {
					foreach ($total->children() as $amount) {
						if ($total->getName() == "account-total") {
							echo "<span class='amount' style='".setTextRed($amount->quantity)."'>".money_format('%!n', (float)$amount->quantity). "</span>";
						}
					}
				}
				
				//Print account's name, when it has value
				if (isset($sub_account->name)) {
					if ($round>1) echo "<div class='border-account' style='border-right:2px solid ".$colors[0].";'> </div>";
					else echo "<div class='border-account'> </div>";
						
					if ($round>1) { 
						//Print block space 
						if ($width>20) {
							echo "<div style='width:".($width-20)."px;float:left;'> </div>";
							$width = 20;
						}
						
						if (!isset($isprinttop ))$isprinttop = false;
						//Print line border-top and right
						if ($isprinttop) {
							echo "<div class='border-subaccount' style='width:".$width."px;float:left;border-right:2px solid ".$colors[$round-1]."'> </div>";
							if ($lastparent == null) $isprinttop = false;
						}
						else {
							echo "<div class='border-subaccount' style='width:".$width."px;float:left;border-right:2px solid ".$colors[$round-1].";border-top:2px solid ".$colors[$round-1]."'> </div>";
							$isprinttop = true;
						}
					}
					
					echo "<span class='account'>".$sub_account->name. "</span>";
					
					// Set url for the link to transaction data
					$filename = createTransactionFileName($sub_account, $rowno);

					echo '<input id="account_name'.$rowno.'" type="hidden" value="'.$filename.'">';
				}
				
				// **Print account name**
				// If only one sub account, display only 1 line
				if ($num_sub_account == 1) {	
					if(isset($under_sub->name)) {
						echo ":".$under_sub->name."";
						
						//Override url for the link to transaction data when there is sub-account
						$filename = createTransactionFileName($under_sub, $rowno);						
						
						echo "<script type=\"text/javascript\">
							var e = document.getElementById('account_name".$rowno."'); e.value='".$filename."'; 
							</script>";
					}
					
					$rowno = print_subaccount($sub_account->children()->account, $round, $num_sub_account, $under_sub->name, $parentname, $rowno);
					echo "</div>"; //End div.container
				}
				else if($num_sub_account > 1){ 
									
					echo "</div>"; //End div.container
					$rowno = print_subaccount($sub_account->children()->account, $round, $num_sub_account, $sub_account->name, $parentname, $rowno);
					
				} 
				
				echo "</div></a>";//End div:subaccount
				
				
			}
		}
		
		return $rowno;
	}

	function setTextRed($value) {
		if ($value<0) return "color: rgb(153,26,0)";
	}
	
	/*
	* Read transactions from xml and insert into an array
	* Return: array of transactions
	* as array(account id, array of transactions data)
	*/
	function getTransactionsToArray($transactions){
		foreach ($transactions as $transaction) {
			$date = $transaction->date.'';
			$code = $transaction->code;
			$payee = $transaction->payee;

			foreach ($transaction->postings->children() as $posting) {
				$id = $name = $quantity = "";
				
				foreach ($posting->account as $account) {
					$id = $account->attributes()->ref;
					$name =  $account->name;
				}
				
				foreach ($posting->{'post-amount'} as $postamount) {
					foreach ($postamount->amount as $amount) {
						$quantity = $amount->quantity;
					}
				}
				
				$data = array("id"=>$id, "name"=>$name, "amount"=>$quantity, "dato"=>$date,"code"=>$code, "payee"=>$payee);

				if (empty($account_trans[$id.''])) $account_trans[$id.''] = array();

				array_push($account_trans[$id.''], $data);
			}
		}
			
		return $account_trans;
	}
	/*
	* Display transactions in table
	* Order by date (low -> high)
	*/
	function printTransactions($transactions) {
		foreach ($transactions as $key=>$accounts) {
			ob_start();
			include ("header.php");
			//echo '<h1>Trasactions</h1>';
			echo '<h1>'.$accounts[0]['name'].'</h1>';
			echo '<table width="70%">';
			echo '<tr>
					<th>Dato</th>
					<th>Code</th>
					<th>Payee</th>
					<th  width="120" align="right">Amount</th>
					<th  width="120" align="right">Saldo</th>
				</tr>';
			
			$account_name = "";
			$saldo = 0;
			
			// sorting by date
			usort($accounts, "cmp");
			
			$csv_filename = createFileName($accounts[0]['name'] , ".csv");
			$fp = fopen(CSV_FOLDER.$csv_filename, 'w');
			
			foreach ($accounts as $account) {
				$saldo += (float)$account['amount'];
				$saldo = round($saldo, 2);
				$amount = round((float)$account['amount'],2);
				if ($account_name == "") $account_name = $account['name'];
				
				echo '<tr>
						<td>'.$account['dato'].'</td>
						<td>'.$account['code'].'</td>
						<td>'.$account['payee'].'</td>
						<td align="right">'.money_format('%!n', $amount).'</td>
						<td align="right">'.money_format('%!n', $saldo).'</td>
					  </tr>';
				
				$csv_data["date"] = "".$account['dato']."";
				$csv_data["code"] = "".$account['code']."";
				$csv_data["payee"] = "".$account['payee']."";
				$csv_data["amount"] = "".money_format('%!n', $amount)."";
				$csv_data["saldo"] = "".money_format('%!n', $saldo)."";
								
				fputcsv($fp, $csv_data, ',', '"');
			}
			
			fclose($fp);
			
			echo '</table>';
			include ("footer.php");
			
			$file = createFileName($account_name , ".html");

			$contents = ob_get_contents();
			
			/*if (!file_exists(HTML_FOLDER) {
				mkdir(HTML_FOLDER);
			}*/
			
			file_put_contents(HTML_FOLDER.$file, $contents );
			ob_end_clean();
			
		}
	}
	
	function createCSVtransactions($accounts) {
		$fp = fopen(CSV_FOLDER.'file.csv', 'w');

		foreach ($accounts as $account) {
			fputcsv($fp, $fields);
		}

		fclose($fp);
	}
	
	/*
	* Check if there is transactions
	* return boolean
	*/
	function hasTransactions($id){
		global $trans_array;
		foreach ($trans_array as $key=>$accounts) {
			if ($key == $id) {
				return true;
				break;
			}
		}
		
		return false;
	}
	
	/*
	* Create transactions html file in /html/ folder
	* return html file's name
	*/
	function createTransactionFileName($account, $rowno) {
		GLOBAL $firstfile;
		$filename = "";
		if(hasTransactions($account->attributes()->id)){
			$filename = createFileName($account->fullname, "");
			if($firstfile == "") $firstfile = $filename;

			echo "<script type=\"text/javascript\">
			var e = document.getElementById('account_row".($rowno)."'); 
			e.style.fontWeight  = 'bold'; 
			e.style.cursor ='pointer';
			
			var csv = document.getElementById('link_csv".($rowno)."'); 
			csv.style.display  = 'inline'; 
			csv.title = '".$account->name."';
			csv.style.cursor ='pointer';
			</script>";

			
		} else {
			$filename = "";
			
			echo "<script type=\"text/javascript\">
			var e = document.getElementById('account_row".($rowno)."'); 
			e.style.fontWeight  = 'normal'; 
			
			var csv = document.getElementById('link_csv".($rowno)."'); 
			csv.style.display  = 'none'; 
			</script>";
		}
		
		return $filename;
	}
	
	
	/* 
	* Create transactions file name
	* return url
	*/
	function createFileName($name, $type) {
		$name = str_replace("Å", "Aa",$name);
		$name = str_replace("å", "aa",$name);
		$name = str_replace("Æ", "Ae",$name);
		$name = str_replace("æ", "ae",$name);
		$name = str_replace("Ø", "Oe",$name);
		$name = str_replace("ø", "oe",$name);
		$name = str_replace(":", "_",$name);
		$url = strtolower(preg_replace('/\s+/', '', $name).$type);
		
		return $url;
	}
	
	/*
	* Sorting transactions by date
	* return int 
	*/
	function cmp($a, $b) {
		if ($a['dato'] == $b['dato']) {
			return 0;
		}
		return ($a['dato'] < $b['dato']) ? -1 : 1;
	}
	
	/*
	* Create frame layout
	* right panel: transactions data
	* left panel: accounts list in tree structure
	*/
	function createFrame() {
		GLOBAL $firstfile;
		
		ob_start();
		
		echo '
		<HTML>
		<HEAD>
		<TITLE>Report</TITLE>
		</HEAD>
		<FRAMESET cols="35%,65%">
		  <FRAME src="accounts.html" >
		  <FRAME name="transaction_page" src="'.$firstfile.'.html">
		</FRAMESET>
		</HTML>';
		
		$contents = ob_get_contents();

		$file = HTML_FOLDER.'index.html';
		file_put_contents($file, $contents );

		ob_end_clean();
	}

	include ("footer.php");  

	$contents = ob_get_contents();

	$file = HTML_FOLDER.'accounts.html';
	file_put_contents($file, $contents );

	ob_end_clean();

	createFrame();
	
?>
