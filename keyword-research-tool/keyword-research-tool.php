<?php
/*
 Plugin Name: The Focus Keyword Tool
 Plugin URI:
 Description: <strong>The Focus Keyword Tool</strong> designed for Wordpress. Enter your keywords, select what is the direction you want to write towards, and the plugin does the researching for what is related for you.
 
 Version: 1.0.0
 Author: Kevin - KW Tech Solutions
 Author URI: https://kwtechsolutions.com.sg
 Author Email: kevin@kwtechsolutions.com.sg
 License:
 
 Copyright 2024  (https://kwtechsolutions.com.sg)
 
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 2, as
 published by the Free Software Foundation.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with this program. If not, see <http://www.gnu.org/licenses/>.
 
 */


if ( ! defined( 'ABSPATH' ) ) exit;

class KWTechFocusKeywordTool {
    // installation etc
    function __construct() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        register_uninstall_hook( __FILE__, 'uninstall_removedata' );
        
        // plugin menu
        add_action( 'admin_menu', 'kwt_the_focus_keyword_tool_plugin_menu' );
        
        if (is_admin() ) {
            include_once( plugin_dir_path( __FILE__ ). '/keywords.php' );
			include_once( plugin_dir_path( __FILE__ ). '/focus.php' );
        }
        
        function kwt_the_focus_keyword_tool_plugin_menu() {
            add_menu_page(
                'The Focus Keyword Tool',
                'Focus Tool',
                'manage_options',
                'focus-keyword-tool',
                'kwt_focus_keyword_tool_options',
                plugins_url( '/images/icon.png', __FILE__ ),
                6
                );
        }
        
        function kwt_focus_keyword_tool_options() {
            if ( !current_user_can( 'manage_options' ) ) {
                wp_die( esc_html( 'You do not have permmission to access this page.' ) );
            }
            
            $pluginUrl = plugin_dir_url( __FILE__ ) ;
            
            ?>
		<style>

			#kwt-kw-table {
				width: 100%;
			}

			.kwt-control {
				width: 100%;
			}

			.kwt-control.selected-keywords {
				padding: 10px;
				margin: 20px auto;
				background: #f9f9f9;
			}

			#kwt-kw-table td,
			#kwt-kw-table th {
				padding: 12px 12px;
			}

			#kwt-kw-table th {
				text-align: left;
			}

			.kwt-column-row {
				clear: both;
				overflow: hidden;
			}

			.kwt-column-left {
				float: left;
				width: 65%;
			}

			#kwt-stick-right {
				float: right;
				width: 25%;
				position: fixed;
				top: 10%;
				right: 0;
				padding-right: 2%;
			}

			.kwt-wp-panel-look {
				float: left;
				width: 100%;
				padding: 20px;
				background: #CCCCCC;
				border: 1px solid #000000;
				box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
				margin: 10px auto;
			}

			.kwt-wp-panel-look.search-input {
				float: left;
				width: 65%;
				padding: 5px 20px;
				background: #FA9500;
				border: 1px solid #000000;
				box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
			}

			#kwt-kw-table select {
				color: #90EAFF;
			}

			#kwt-kw-table td {
				border: 1px solid #000000;
			}

			#kwt-kw-table th {
				border: 1px solid #000000;
			}

			#kw-search-submit.button.button-success {
				background-color: #D72A2D;
				color: #FFFFFF;
				border-color: #000000;
				font-size: 15px;
			}

			.kwt-search-holder {
				margin-left: 0px;
			}

			.kwt-search-holder .dashicons {
				font-size: 36px;
				padding: 4px 40px 0 0px;
				color: #D72A2D;
			}

			#kw-search-submit {
				height: 45px;
			}

			#kw-search-input {
				width: 35%;
				padding: 7px 10px 7px 10px;
			}

			.kwt-footer {
				width: 65%;
			}

			.kwt-footer a {
				text-decoration: none;
			}

			.kwt-footer .kwt-logo {
				max-width: 20px;
				margin-bottom: -6px;
				display: inline-block;
				padding: 0 0px 0 7px;
			}

			.kwt-footer .linkedin {
				max-width: 17px;
				margin-bottom: -4px;
			}

			.keyword-tool a.back-link {
				color: #000000;
			}

			.wrap h2.kwt-title {
				padding-top: 25px;
			}

			.additional-search .dashicons-search:before {
				font-size: 36px;
				display: inline-block;
				color: #D72A2D;
			}

			#kwt-kw-table tr:hover td {
				background: #90EAFF;
			}

			.keyword-tool table .dashicons {
				color: #aaa;
				padding: 10px;
				cursor: pointer;
			}

			.keyword-tool table .dashicons:hover {
				color: #000;
			}

			.keyword-tool .container-checkbox {
				display: block;
				position: relative;
				padding-left: 35px;
				margin-bottom: 12px;
				cursor: pointer;
				font-size: 22px;
				-webkit-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
			}

			.keyword-tool .container-checkbox input {
				position: absolute;
				opacity: 0;
				cursor: pointer;
				height: 0;
				width: 0;
			}

			.keyword-tool .checkmark {
				position: absolute;
				top: 20%;
				left: 0;
				height: 25px;
				width: 25px;
				background-color: #f9f9f9;
				border: 1px solid #000000;
			}

			.keyword-tool .container-checkbox:hover input~.checkmark {
				background-color: #f9f9f9;
				border: 1px solid #000000;
			}

			.keyword-tool .container-checkbox input:checked~.checkmark {
				background-color: #F9F9F9;
				border: 1px solid #000000;
			}

			.kwt-table-focus-text { 
				font-size: 15px;
				font-weight: normal;
				display: inline-table;
			}

			.keyword-tool .container-checkbox .table-text {


				font-size: 15px;
				font-weight: normal;
				display: inline-table;
				margin-left: 20px;

			}

			#kwt-kw-table .table-text {
				padding-top: 8px;
			}

			.keyword-tool .checkmark:after {
				content: "";
				position: absolute;
				display: none;
			}

			.keyword-tool .container-checkbox input:checked~.checkmark:after {
				display: block;
			}

			.keyword-tool .container-checkbox .checkmark:after {
				left: 9px;
				top: 5px;
				width: 4px;
				height: 9px;
				border: solid #000000;
				border-width: 0 3px 3px 0;
				-webkit-transform: rotate(45deg);
				-ms-transform: rotate(45deg);
				transform: rotate(45deg);
			}

			.kwt-hide {
				display: none;
			}

		</style>
            
            
            <script>
			
			jQuery(document).ready(function($){
				$.fn.timedDisable = function(time) {
				if (time == null) { time = 5000; }
				return $(this).each(function() {
					$(this).attr('disabled', 'disabled');
					var disabledElem = $(this);
					setTimeout(function() {
						disabledElem.removeAttr('disabled');
					}, time);
				});
			}
			});
			
			jQuery(function() {
				
				function runTools (keywordInput) {	
					var kwt_data = {
						'action': 'kwt_initial_keywords',
						'keyword': keywordInput, 
						'security': '<?php echo esc_js( wp_create_nonce( "kwt-ajax-nonce" ) ); ?>'
					};
					
					jQuery.post(ajaxurl, kwt_data, function(response) {
						
						
						kw_data = JSON.parse(response); 

						jQuery('#kwt-kw-table tbody').empty();
						jQuery('#KWT-Replace-Heading').text('Keyword Suggestion')
						jQuery('#kwt-replace-weight').text("Use As Initial Keyword");
						
						// array empty or does not exist
						if ( kw_data[0] === undefined || kw_data[0].length == 0) {
							jQuery('#kwt-kw-table').append('<tr><td></td><td colspan="2"> Sorry, no results found for your input.</td></tr>');
						} else {
						
							var arrayLength = kw_data.length;
							for (var i = 0; i < arrayLength; i++) {
								var keyword = kw_data[i];
								
								var keywordResultClean = keyword.replace(/(<([^>]+)>)/ig,"");
								
								jQuery('#kwt-kw-table').append('<tr><td>'+(i+1)+'</td><td class="checkbox-select"><label class="container-checkbox"><span class="table-text">'+keyword+'</span><input type="checkbox" name="select-kw" class="checkbox-select" value="'+keywordResultClean+'" /><span class="checkmark"></span></label></td><td class="additional-search"><span title="Get keywords"data-keyword="'+keywordResultClean+'" class="dashicons dashicons-search"></span></td></tr>');
						
							}
						}		
						
						return kw_data;
						
					});

					

				}
				
			jQuery(function() {	
				// remove textarea items
				jQuery('.clear-checkbox').click(function (e) {    
					var txt = jQuery(this).text();
						jQuery('input:checkbox').prop('checked',false);            
						jQuery('#selected-keywords').val(''); 
						jQuery('button.check-checkbox').text('Check All');
				});
							
				function processKwDataInput (kwtKeyword){
					if(kwtKeyword){
						jQuery('#kw-search-input').val(kwtKeyword);
					}else{
						var kwtKeyword = jQuery('#kw-search-input').val();
					}
					if (kwtKeyword != '') {
						jQuery('#kw-search-input').timedDisable(30000);
						var dataOutput  = runTools(kwtKeyword); 
					}
				}
								
				// press search button
				jQuery('#kw-search-submit').click(function (e)  {
					jQuery('#kw-search-submit').timedDisable(30000);
					processKwDataInput ('');
				});
				 
				// enter search input field 
				jQuery("#kw-search-input").keypress(function(e) {
					if(e.which == 13) {
						jQuery('#kw-search-input').timedDisable(30000);
						processKwDataInput ('');
					}
				});
				
				// click search icon
				jQuery(document).on('click', '.additional-search .dashicons-search', function(){
					var kwtKeyword = jQuery(this).data('keyword');
					jQuery('.additional-search .dashicons-search').timedDisable(30000);
					processKwDataInput (kwtKeyword);
				});
				
				// click on find focus words
				jQuery(document).on('click', '#trigger-focus-search', function(){
					jQuery('#trigger-focus-search').timedDisable(180000);
					//get keywords separated by a new line 
					if (jQuery("#selected-keywords").val() != ''){
						var kwt_split_items = {
							'action': 'kwt_focus_keywords',
							'keywords': jQuery("#selected-keywords").val().split(/\r?\n/),
							'security': '<?php echo esc_js( wp_create_nonce( "kwt-ajax-nonce" ) ); ?>'
						};
						//send to focus_search.php
						jQuery.post(ajaxurl, kwt_split_items, function(response) {
							godBless = jQuery.parseJSON(response);
							jQuery('#kwt-kw-table tbody').empty();
							jQuery('#KWT-Replace-Heading').text("Focus Word Suggestion");
							jQuery('#kwt-replace-weight').text("Importance");
							
							if (godBless.hasOwnProperty("")) {
								delete godBless[""];
							}
							
							arrayLength = Object.keys(godBless).length;
							if ( Object.keys(godBless) === undefined || arrayLength == 0) {
								jQuery('#kwt-kw-table').append('<tr><td></td><td colspan="2"> Sorry, no results found for your input.</td></tr>');
							} else {
								i = 1;
								for ([xa, ya] of Object.entries(godBless)) {
									jQuery('#kwt-kw-table').append('<tr><td>'+i+'</td><td><span class="kwt-table-focus-text">'+xa+'</span></td><td><span class="kwt-table-focus-text">'+ya+'</span></td>');
									i++;
								}
							}		
						});
						
						//set timer to prevent resending, remember to do it for the search functions too (grouped and bound to the same timer for search)
					}				
				});
				
			});
			
			

			
			jQuery(document).on('click', '.checkbox-select input', function(){
				
				var checkedVal = jQuery(this).val();
				
				if (jQuery(this).prop('checked')){
					var currentKeywords = jQuery('.selected-keywords').val(); 	
					jQuery('.selected-keywords').val(currentKeywords+(currentKeywords!=''? '\r\n' : '')+checkedVal);
				} else {
					var allVals = [];
					var currentKeywords = jQuery('.selected-keywords').val();
					
					var currentKeywordsArray = currentKeywords.split('\n');
					
					var filtered = currentKeywordsArray.filter(function (el) {
					  return el != checkedVal;
					});
					
					var filteredKeywordsArray = filtered;

					var stringValues = filteredKeywordsArray.join('\r\n');	
					jQuery('.selected-keywords').val(stringValues);

					
					
				}
				var currentKeywords = jQuery('.selected-keywords').val();
				 
			})});		

			
				
			</script>
            
            <?php	
			

			
		
			
			echo '
			
	<div class="wrap keyword-tool">
		<h2 class="kwt-title"><span class="dashicons dashicons-search"></span> The Focus Keyword Tool</h2>
		<p>Let us find your <strong>focus keyword</strong> for you, discovering what is important to your topic. </p>
		<div class="kwt-wp-panel-look search-input">
			<p class="kwt-search-holder">
				<span class="dashicons dashicons-arrow-right-alt"></span>
				<input type="search" id="kw-search-input" name="s" placeholder="Keyword" value="">
				<input type="submit" id="kw-search-submit" class="button button-success" value="Search">
			</p>
		</div>


		<div class="kwt-column-row">
			<div class="kwt-column-left">
				<div class="kwt-wp-panel-look">
					<table id="kwt-kw-table" class="striped">
						<thead>
							<tr>
								<th style="width:5%;">#</th>
								<th id="KWT-Replace-Heading" style="width:80%;">Keyword</th>
								<th id="kwt-replace-weight" style="width:15%;">Use As Initial Keyword</th>
							</tr>
							<thead>
							<tbody>
								<tr>
									<td></td>
									<td colspan="2"> Enter your keyword to generate a list of related search queries.
									</td>
								</tr>

							</tbody>
					</table>
				</div>
			</div>

			<div id="kwt-stick-right">
				<div class="kwt-wp-panel-look">
					<h2>Term Searched</h2>
					<p>Remember to use javascript to load the relevant information</p>
				</div>
				<div class="kwt-wp-panel-look">
					<div class="panel-group">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h2><span class="dashicons dashicons-editor-alignleft"></span> Selected keywords</h2>
							</div>
							<div id="questions-box" class="panel-collapse collapse in" aria-expanded="true" style="">
								<div class="panel-body">
									<textarea id="selected-keywords" class="kwt-control selected-keywords"
										placeholder="Please separate your keywords with a new line. Selected keywords will automatically appear in this box. Please be patient with the focus tool. It needs time to think."
										rows="10"></textarea>
									<button type="button" class="button clear-checkbox"> <span
											class="dashicons dashicons-trash"></span> Clear</button>
									<button class="button button find-focus feedback-message"
										data-message="Starting Search" data-autoclose="1" id="trigger-focus-search"><span
											class="dashicons dashicons-code-standards""></span>Find Focus Words</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="kwt-wp-panel-look kwt-footer ">
			<p>Developed by: <a class="back-link"
					href="https://kwtechsolutions.com.sg/" target="_blank"> KW Tech Solutions</a>
				-- Like  plugin? Consider making a <a class="button button"
					href="https://paypal.me/sgkwtechsolutions"
					target="_blank">Donation <span class="dashicons dashicons-thumbs-up"></span> </a> </p>
		</div>
	</div>';
			
			
		
		}	
		
    } // end constructor
 
 
    public function activate( $network_wide ) {   
    } // end activate
 
    public function deactivate( $network_wide ) {     
    } // end deactivate
    
    public function uninstall( $network_wide ) {
    } // end uninstall
 
 
    /*--------------------------------------------*
     * Core Functions
     *---------------------------------------------*/


 
} // end class
 
$plugin_name = new KWTechFocusKeywordTool();