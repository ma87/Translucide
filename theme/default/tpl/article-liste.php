<?
if(!$GLOBALS['domain']) exit;

?>

<style>
.resume { border-left: 0.2em solid  #35747f; }
</style>

<section class="w700p mod center mtm mbl">

	<h1 class="mbn tc"><?txt('title')?></h1>

	<div class="mts tc italic">
		<?
		_e("Catégories : ");

		// Liste les tags pour filtrer la page
		$i = 1;
		$sel_tag_list = $connect->query("SELECT distinct cle, val FROM ".$table_meta." WHERE type='tag' ORDER BY ordre ASC, cle ASC");
		while($res_tag_list = $sel_tag_list->fetch_assoc()) {
			if($i > 1) echo', ';
			echo'<a href="'.make_url($res['url'], array($res_tag_list['cle'], 'domaine' => true)).'" class="color tdn dash">'.$res_tag_list['val'].'</a>';
			$i++;
		}
		?>
	</div>


	<?
	// Si on n'a pas les droits d'édition des articles on affiche uniquement ceux actifs
	if(!@$_SESSION['auth']['edit-article']) $sql_state = "AND state='active'";
	else $sql_state = "";

	// Navigation par page
	$num_pp = 5;

	if(isset($GLOBALS['filter']['page'])) $page = (int)$GLOBALS['filter']['page']; else $page = 1;

	$start = ($page * $num_pp) - $num_pp;

	// Constricton de la requete
	$sql ="SELECT SQL_CALC_FOUND_ROWS ".$tc.".id, ".$tc.".* FROM ".$tc;

	// Si filtre tag
	if(isset($res_tag['cle']))
	$sql.=" RIGHT JOIN ".$table_meta."
	ON
	(
		".$table_meta.".id = ".$tc.".id AND
		".$table_meta.".type = 'tag' AND
		".$table_meta.".cle = '".$res_tag['cle']."'
		)";

		/*	$sql.=" WHERE (".$tc.".type='article' OR ".$tc.".type='event') AND ".$tc.".lang='".$lang."' ".$sql_state."
		ORDER BY ".$tc.".date_insert DESC
		LIMIT ".$start.", ".$num_pp;*/
		$sql.=" WHERE (".$tc.".type='article') AND ".$tc.".lang='".$lang."' ".$sql_state."
		ORDER BY ".$tc.".date_insert DESC
		LIMIT ".$start.", ".$num_pp;

		$sel_fiche = $connect->query($sql);

		$num_total = $connect->query("SELECT FOUND_ROWS()")->fetch_row()[0];// Nombre total de fiche

		while($res_fiche = $sel_fiche->fetch_assoc())
		{
			// Affichage du message pour dire si l'article est invisible ou pas
			if($res_fiche['state'] != "active")
			$state = " <span class='deactivate pat'>".__("Article d&eacute;sactiv&eacute;")."</span>";
			else
			$state = "";

			$content_fiche = json_decode($res_fiche['content'], true);

			$date = explode("-", explode(" ", $res_fiche['date_insert'])[0]);

			?>
			<article class="resume mod plm mrm mtl">

				<!--<div class="date fl mrm prm up bold big tc">
				<div class="bigger"><?=$date[2]?></div>
				<div><?=trim(utf8_encode(strftime("%h", mktime(0, 0, 0, $date[1], 10))),".")?></div>
			</div>-->

			<h2 class="mts up bigger"><a href="<?=make_url($res_fiche['url'], array("domaine" => true));?>" class="tdn"><?=$res_fiche['title']?></a><?=$state?></h2>

			<?if(isset($content_fiche['texte'])) echo word_cut($content_fiche['texte'], '350')."...";?>

			<div class="fr"><a href="<?=make_url($res_fiche['url'], array("domaine" => true));?>" class="bt bg-color bold"><?_e("Lire l'article")?></a></div>


		</article>
		<?
	}

	page($num_total, $page);

	?>
</section>
