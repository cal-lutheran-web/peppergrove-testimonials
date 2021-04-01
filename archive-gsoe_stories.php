<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title></title>
</head>
<body>


<?php

	$all_programs = get_terms(array(
		'taxonomy' => 'gsoe_programs',
  		'hide_empty' => true,
	));

	$all_posts = get_posts(array(
		'numberposts' => -1,
		'post_type' => 'gsoe_stories'
	));

	$all_class_years = array();

	foreach($all_posts as $key=>$p){
		array_push($all_class_years, get_field('class_year',$p->ID));
	}

	$all_class_years = array_unique(array_filter($all_class_years));
	asort($all_class_years);
	

?>

<h2>Alumni Achievements</h2>



<form method="get" id="program-filter">

<div class="row">
	<div class="col-sm-6">
		<label for="program-select">View by Program</label><br />
		<select id="program-select" name="program" onchange="this.form.submit()" class="full-size"><option value="">All Programs</option>
			<?php foreach($all_programs as $key=>$term){
				$is_selected = (isset($_GET['program']) && $_GET['program'] == $term->slug) ? 'selected' : '';
				echo '<option value="'.$term->slug.'" '.$is_selected.'>'.$term->name.'</option>';
			} ?> 
		</select>
	</div>
	<div class="col-sm-6">
		<label for="year-select">View by Class Year</label><br />
		<select id="year-select" name="class_year" onchange="this.form.submit()"><option value="">All Class Years</option>
			<?php foreach($all_class_years as $year){
				$is_selected = (isset($_GET['class_year']) && $_GET['class_year'] == $year) ? 'selected' : '';
				echo '<option value="'.$year.'" '.$is_selected.'>'.$year.'</option>';
			} ?>
		</select>
	</div>
</div>
</form>


<hr />



<?php

	// get program for query
	if(isset($_GET['program']) && !empty($_GET['program'])){
		
		$program_query = array(
			array(
				'taxonomy' => 'gsoe_programs',
				'field'    => 'slug',
				'terms'    => $_GET['program']
			)
		);
	}

	// get class year for query
	$class_year = (isset($_GET['class_year']) && !empty($_GET['class_year'])) ? $_GET['class_year'] : '';



	// The Query
	$query = new WP_Query(array(
		'post_type' => 'gsoe_stories',
		'numberposts' => -1,
		'tax_query' => $program_query,
		'meta_key' => 'class_year',
		'meta_value' => $class_year
	));

	// The Loop
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$program = get_the_terms($post->ID, 'gsoe_programs')[0]->name;

			$aside = get_the_post_thumbnail($post->ID, 'medium');

			$content = '
				<h2>'.get_field('first_name').' '.get_field('last_name').' '.get_class_year(get_field('class_year')).'</h2>
				<p><strong>'.$program.'</strong></p>
				'.get_field('description').'
			';
			
			echo short_item($aside,$content);
			
		}
	} else {
		// no posts found
	}

	// Restore original Post Data
	wp_reset_postdata();


?>

</body>
</html>