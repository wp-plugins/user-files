<?php
/*
Plugin Name: User File Manager
Plugin URI: http://www.whereyoursolutionis.com
Description: Plugin to manage files for your users. You can upload files for your users to access, files upoloaded to the user account are only viewable by the designated user. Added options for user to add and/or delete files.
Author: Innovative Solutions
Version: 1.0.4
Author URI: http://www.whereyoursolutionis.com
*/


register_activation_hook(__FILE__,'ActivateFileDir');
//register_Deactivation_hook(__FILE__,'DectivateFileDir'); 
add_action('admin_menu', 'show_FM_pages');
add_action('wp_dashboard_setup', 'file_manager_dashboard');




function show_FM_pages() {

    add_options_page('File Manger', 'File Manager', 'manage_options', 'file_manager_options', 'files_settings_page' );



	add_menu_page( 'Manage Files', 'Manage Files', 'manage_options', 'manage-files-main', 'manage_files_mainpg');

	add_submenu_page('manage-files-main', 'Add Files', 'Add Files', 'manage_options','files-add-files', 'manage_files_upload');

$currOpts_menu = get_option('file_manger_show_menu');

	
	if (!current_user_can('manage_options') and $currOpts_menu==yes)  {
	
	add_menu_page( 'Manage Files', 'Manage Files', 'read', 'manage-files-user', 		'manage_files_user');
	
	}


}

	
function ActivateFileDir() {
$upload_dir = wp_upload_dir();

$isFolder = file_exists ( $upload_dir['basedir'].'/file_uploads'); 

if (!$isFolder) {
mkdir (  $upload_dir['basedir'].'/file_uploads', 0777 , true );
chmod($upload_dir['basedir'].'/file_uploads', 0777);
}

add_option('file_manger_show_dash', 'yes');
add_option('file_manger_show_menu', 'yes');
add_option('file_manger_allow_del', 'no');
add_option('file_manger_allow_up', 'no');



}

function DectivateFileDir() {
$upload_dir = wp_upload_dir();

$isFolder = file_exists ($upload_dir['basedir'].'/file_uploads');

	if ($isFolder) {
	
    $files = glob( $isFolder . '*', GLOB_MARK );
    foreach( $files as $file ){
        if( substr( $file, -1 ) == '/' )
            delTree( $file );
        else
            unlink( $file );
    }
    $isitGone = rmdir( $isFolder);


		if ($isitGone) {

		echo '<div id="message" class="updated">';
		echo "The folder has been deleted";
		echo '</div>';
		} else{
		echo '<div id="message" class="error">';
		echo "There was an error deleting the folder, please try again!";
		echo '</div>';
		}
	}


}


function Checkin_userfile_opts(){


$currOpts_dash = get_option('file_manger_show_dash');
$currOpts_menu = get_option('file_manger_show_menu');
$currOpts_up = get_option('file_manger_allow_up');
$currOpts_del = get_option('file_manger_allow_del');

if($currOpts_dash and !$currOpts_up) {

echo '<div id="message" class="updated">';
echo 'New options have been added to the file manager options, you can change these settings <a href="'.ABSPATH.'/wp-admin/options-general.php?page=file_manager_options">here</a>';
echo '</div>';

}



	if(!$CurrOpts_dash){

	add_option('file_manger_show_dash', 'yes');
	}

	if(!$CurrOpts_menu){
	add_option('file_manger_show_menu', 'yes');
	}
	if(!$CurrOpts_up){
	add_option('file_manger_allow_del', 'no');
	}
	if(!$CurrOpts_del){
	add_option('file_manger_allow_up', 'no');
	}


}




	
function files_settings_page() {
Checkin_userfile_opts(); ?>
<h2>File Manager Options</h2>
<p>
 
<?php if ($_GET['full_uninstall']==true) {
DectivateFileDir();

} 



if ($_POST['update']) {

$currOpts_dash = get_option('file_manger_show_dash');
$currOpts_menu = get_option('file_manger_show_menu');
$currOpts_up = get_option('file_manger_allow_up');
$currOpts_del = get_option('file_manger_allow_del');

	if ($_POST['file_manger_show_dash'] != $currOpts_dash ) {
	
		if($_POST['file_manger_show_dash']=='yes') {
		update_option('file_manger_show_dash','yes' );
		}else{
		update_option('file_manger_show_dash','no' );
		}
	}
	
	if($_POST['file_manger_show_menu'] != $currOpts_menu ) {
		if($_POST['file_manger_show_menu']=='yes') {
		update_option('file_manger_show_menu','yes' );
		}else{
		update_option('file_manger_show_menu','no' );
		}
	}
	
	if($_POST['file_manger_allow_del'] != $currOpts_del ) {
		if($_POST['file_manger_allow_del']=='yes') {
		update_option('file_manger_allow_del','yes' );
		}else{
		update_option('file_manger_allow_del','no' );
		}
	}
	
	if($_POST['file_manger_allow_up'] != $currOpts_up ) { 
		if($_POST['file_manger_allow_up']=='yes') {
		update_option('file_manger_allow_up','yes' );
		}else{
		update_option('file_manger_allow_up','no' );
		}
	}

	echo '<div id="message" class="updated fade">Settings Saved</div>';
	
}

$currOpts_dash = get_option('file_manger_show_dash');
$currOpts_menu = get_option('file_manger_show_menu');
$currOpts_up = get_option('file_manger_allow_up');
$currOpts_del = get_option('file_manger_allow_del');

 ?>
 <table class="form-table">

	<tr> 
	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<td><input type="checkbox" name="file_manger_show_dash" value="yes" <?php if ($currOpts_dash== 'yes'){ echo 'checked'; }?>> Show Dashboard Widget<br></td>
    </tr>  
	
	<tr>
	<td><input type="checkbox" name="file_manger_show_menu" value="yes" <?php if ($currOpts_menu== 'yes'){ echo 'checked'; }?>> Show Menu Item<br></td>
    </tr>  
	<tr>
	<td><input type="checkbox" name="file_manger_allow_up" value="yes" <?php if ($currOpts_up== 'yes'){ echo 'checked'; }?>> Allow users to add files<br></td>
    </tr>  
	<tr>
	<td><input type="checkbox" name="file_manger_allow_del" value="yes" <?php if ($currOpts_del== 'yes'){ echo 'checked'; }?>> Allow users to delete files<br></td>
    </tr>  
	
	<tr><td>
	<input type='hidden' name ='update' value='update'>
	<input type='submit' value='<?php _e('Save Options'); ?>' class='button-secondary' /></td>

</form>
	
	</tr>
	
</table>
		
		
		






<?php
}

function manage_files_mainpg() {  
Checkin_userfile_opts();

$upload_dir = wp_upload_dir();

if (isset($_GET['deletefile'])){

$isitGone = unlink($_GET['deletefile']);

		if ($isitGone) {

		echo '<div id="message" class="updated">';
		echo "The file has been deleted";
		echo '</div>';
		} else{
		echo '<div id="message" class="error">';
		echo "There was an error deleting the file, please try again!";
		echo '</div>';
		}
}

if (isset($_GET['deletefolder'])){

$dir = $_GET['deletefolder'].'/'; 
    $files = glob( $dir . '*', GLOB_MARK );
    foreach( $files as $file ){
        if ($file != "." and $file !=".."){
		unlink( $file );

		}
	}
    $isitGone = rmdir( $dir );


		if ($isitGone) {

		echo '<div id="message" class="updated">';
		echo "The folder has been deleted";
		echo '</div>';
		} else{
		echo '<div id="message" class="error">';
		echo "There was an error deleting the folder, please try again!";
		echo '</div>';
		}
}



if ($handle = opendir($upload_dir['basedir'].'/file_uploads')) {

echo '<h3>User Files</h3>';

echo '<table class="widefat">';
while (false !== ($file = readdir($handle))) {
		
		if ($file!=".") {
			if ($file!="..") {
				$userNum=(int)$file;
				$user_info = get_userdata($userNum);
				echo '<thead>';
				echo '<th>'.$user_info->first_name. ' '.$user_info->last_name .' | '.$user_info->user_email.'</th>';
				echo '<th><a href="admin.php?page=manage-files-main&deletefolder='.$upload_dir[						'basedir'].'/file_uploads/'.$userNum .'">Delete Folder</a></th></thead>';
				
								
					if ($Subhandle = opendir($upload_dir['basedir'].'/file_uploads/'.$userNum)) {
				
						while (false !== ($files = readdir($Subhandle))) {
						echo '<tr>';
							if ($files!=".") {
								if ($files!="..") {
								echo '<td><a href="'.$upload_dir['baseurl'].'/file_uploads/'.$userNum .'/'.$files .'">'.$files.'</a></td>';
								echo '<td><a href="admin.php?page=manage-files-main&deletefile='.$upload_dir['basedir'].'/file_uploads/'.$userNum .'/'.$files.'">delete</a></td>';
								}
							}
						echo '</tr>';
						}
					
					}else{
					echo 'No Files';
					}  
			
			}
		}
    }

echo '</table>';	
}
	

	
}


function manage_files_upload() { 
Checkin_userfile_opts();
global $wpdb;
echo '<p><h3>Upload Files</h3></p></p>';

$upload_dir = wp_upload_dir();


	if (isset($_POST['curr_user'])) {
	
	$subDir = $_POST['curr_user'];
	
	$usFolder = file_exists ( $upload_dir['basedir'].'/file_uploads/'.$subDir); 

	if (!$usFolder) {
	mkdir ( $upload_dir['basedir'].'/file_uploads/'. $subDir, 0777 , true );
	chmod($upload_dir['basedir'].'/file_uploads/'. $subDir,0777);
	}
	
	
	$target_path = $upload_dir['basedir'].'/file_uploads/'. $subDir.'/';
	
	$target_path = $target_path . basename($_FILES['uploadedfile']['name']); 

	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
		echo '<div id="message" class="updated">';
		echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
		" has been uploaded";
		echo '</div>';
	} else{
		echo '<div id="message" class="error">';
		echo "There was an error uploading the file, please try again!<br />";
		echo $php_errormsg;
		echo '</div>';
	}

	
	
	}
?> 

<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'] .'?page=files-add-files' ?>" method="POST">
<?php

$order = 'user_nicename';
$aUsersID = $wpdb->get_col("SELECT ID FROM $wpdb->users ORDER BY $order");

	echo 'Files for user: <br /><select name="curr_user" id="curr_user">';
	foreach ( $aUsersID as $iUserID ) :
	
	$user_info = get_userdata( $iUserID );  ?>


<option value=<?php echo '"'.$iUserID . '">'. $user_info->user_login; ?> </option>

<?php
endforeach;
echo '</select><br /><p>';

$max_post = (int)(ini_get('post_max_size'));

?>

Choose a file to upload, your upload limit is <?php echo $max_post; ?>M <br /> <input name="uploadedfile" type="file" /><br />
<input type="submit" value="Upload File" />
</form>




<?php
}

function manage_files_user() {

$upload_dir = wp_upload_dir();
global $current_user;
      get_currentuserinfo();

if (isset($_GET['deletefile'])){

$isitGone = unlink($_GET['deletefile']);

		if ($isitGone) {

		echo '<div id="message" class="updated">';
		echo "The file has been deleted";
		echo '</div>';
		} else{
		echo '<div id="message" class="error">';
		echo "There was an error deleting the file, please try again!";
		echo '</div>';
		}
}

if (isset($_POST['addfiles'])){	
			$target_path = $upload_dir['basedir'].'/file_uploads/'. $current_user->ID.'/';
			
			$target_path = $target_path . basename($_FILES['uploadedfile']['name']); 

			if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
				echo '<div id="message" class="updated">';
				echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
				" has been uploaded";
				echo '</div>';
			} else{
				echo '<div id="message" class="error">';
				echo "There was an error uploading the file, please try again!<br />";
				echo $php_errormsg;
				echo '</div>';
			}
	}



echo '<table class="widefat">';	
echo'<thead><th>Your Files</th><th></th></thead>';
	if ($handle = opendir($upload_dir['basedir'].'/file_uploads/'.$current_user->ID)) {
		echo '<b><h5>Right click and select "save as" or "download" to save files</h5></b>';
	while (false !== ($file = readdir($handle))) {
			
			if ($file!=".") {
				if ($file!="..") {
				echo '<tr><td><a href="'.$upload_dir['baseurl'].'/file_uploads/'.$current_user->ID .'/'.$file.'">'.$file .'</a></td>';
				
				$currOpts_up = get_option('file_manger_allow_del');
				
				if($currOpts_up=='yes') {
				echo '<td><a href="admin.php?page=manage-files-user&deletefile='.$upload_dir['basedir'].'/file_uploads/'.$current_user->ID .'/'.$file.'">delete</a></td></tr>';
				}else{
				echo '<td></td></tr>';
				
				}//end if
				
				
				}
			}
		}
	}else{
	echo'You have no files';
	}

echo '</table>';

	$currOpts_up = get_option('file_manger_allow_up');

		if ($currOpts_up=='yes'){  ?> 
		<p><p><p><p><p>
		
		<table class="widefat"><tr><thead><th><h3>Add Files</h3></th></thead></tr>

	<?php

		$max_post = (int)(ini_get('post_max_size'));

		$MaxSet=1000000*(int)$max_post;

		?>

		<tr><td>
		<form enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">		
		Choose a file to upload, your upload limit is <?php echo $max_post; ?>M <br />
		
		 <input name="uploadedfile" type="file" /><br />
		 <input type="hidden" name="addfiles" value="addfiles" />
		<input type="submit" value="Upload File" />
		</form>
		</td></tr>
		<?php
		echo '</table>';
		}




}


function file_manager_dashboard() {
$currOpts_dash = get_option('file_manger_show_dash');

	if($currOpts_dash=='yes') {
	wp_add_dashboard_widget( 'my_wp_file_manager', __( 'Your Files' ),'my_wp_file_manager' );
	} 
}


function my_wp_file_manager() {
$upload_dir = wp_upload_dir();
global $current_user;
      get_currentuserinfo();

	
	if ($handle = opendir($upload_dir['basedir'].'/file_uploads/'.$current_user->ID)) {
		echo '<b>Right click and select "save as" to download files</b><br />';
	while (false !== ($file = readdir($handle))) {
			
			if ($file!=".") {
				if ($file!="..") {
				echo '<a href="'.$upload_dir['baseurl'].'/file_uploads/'.$current_user->ID .'/'.$file.'">'.$file .'</a><br />';
				}
			}
		}
	}else{
	echo'You have no files';
	}



}


?>