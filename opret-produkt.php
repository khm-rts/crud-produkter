<?php
// Inkludér fil der etablerer forbindelsen til databasen
require 'db_config.php';

// Hvis navn er defineret i $_POST, ved vi at vi har sendt vores formular og så kan vi køre dette kode mellem {}
if ( isset($_POST['navn']) )
{
	// Hent alle værdier fra formular, vha. $_POST, da vi har valgt method="post", på vores form. Vi gemmer i variabler med samme navn, som name på vores inputs
	// Vi skal sikre os imod SQL injections, samt farlige tegn, som f.eks. '. Derfor bruger vi funktionen mysqli_real_escape_string() på alle stregne (værdi med tekst og eevt. tegn). Vi bruger floatval, på decimalværdier, samt intval() på hele tal. Vær særlig opmærksom på at bruge disse, så snart der hentes data vha. $_POST, $_GET, $_SESSION, hvor vi bruger værdier i en SQL-sætning.
	$navn			= mysqli_real_escape_string($link, $_POST['navn']);
	$pris			= floatval($_POST['pris']);
	$aar			= intval($_POST['aar']);
	$varenr 		= intval($_POST['varenr']);
	$beskrivelse	= mysqli_real_escape_string($link, $_POST['beskrivelse']);
	$designer		= intval($_POST['designer']);
	$kategori		= intval($_POST['kategori']);

	// Forespørgsel til aT oprette produktet i databasen
	$query =
		"INSERT INTO
			produkter (produkt_navn, produkt_pris, produkt_design_aar, produkt_vare_nr, produkt_beskrivelse, fk_designer_id, fk_kategori_id)
		VALUES ('$navn', $pris, $aar, $varenr, '$beskrivelse', $designer, $kategori)";

	// Send forespørgsel til databassen og gem resultat i variablen $result
	$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

	header('Location: opret-produkt.php?status=success');
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Opret produkt</title>
</head>
<body>
	<h1>CRUD Produkter</h1>

	<h2>Opret produkt</h2>

	<hr>

	<form method="post">
		<p>
			<label>
				Navn:
				<input type="text" name="navn" value="" required maxlength="50">
			</label>
		</p>

		<p>
			<label>
				Pris:
				<input type="number" step="0.25" lang="da" name="pris" required min="0" max="999999.99" value="">
			</label>
		</p>

		<p>
			<label>
				Design år:
				<input	type="number" name="aar" required min="1" max="9999" value="<?php echo date('Y') // Udskriv aktuelt årstal ?>">
			</label>
		</p>

		<p>
			<label>
				Vare nr.:
				<input type="number" name="varenr" id="test" min="100000" max="16777215" required value="">
			</label>
		</p>

		<p>
			<label>
				Beskrivelse:
				<textarea name="beskrivelse" required cols="30" rows="4" style="resize: none"></textarea>
			</label>
		</p>

		<p>
			<label>
				Designer:

				<?php
				// Hent alle designere fra databasen og sorter dem alfabetisk
				$query =
					"SELECT
						*
					FROM
						designere
					ORDER BY
						designer_navn";

				// Send forespørgsel til databassen og gem resultat i variablen $result
				$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

				// Send forespørgsel til databassen og gem resultat i variablen $result
				/*$result = mysqli_query($link, $query) or die( 'Der er fejl i forespørgsel, linje: ' . __LINE__ . ' i filen: ' . __FILE__ . '<pre>Forespørgsel: ' . $query . '</pre> Databasefejl: ' . mysqli_error($link) );*/
				?>
				<select name="designer" required>
					<option value="" hidden>Vælg designer...</option>
					<?php
					// Brug funktion mysqli_fetch_assoc() til at hente data fra vores forespørgsel og returnere det som et assoc array og gem det i variblen $row. Vi bruger while-løkke til at løbe igennem alle rækker af designere
					while( $row = mysqli_fetch_assoc($result) )
					{
						// Udskriv designerens id og navn fra databasen
						// echo '<option value="' . $row['designer_id'] . '">' . $row['designer_navn'] . '</option>';
						?>
						<option value="<?php echo $row['designer_id'] ?>"><?php echo  $row['designer_navn'] ?></option>
						<?php
					}
					?>
				</select>
			</label>
		</p>

		<p>
			<label>
				Kategori:

				<?php
				// Hent alle kategorier fra databasen og sorter dem alfabetisk
				$query =
					"SELECT
						*
					FROM
						kategorier
					ORDER BY
						kategori_navn";

				// Send forespørgsel til databassen og gem resultat i variablen $result
				$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

				// Send forespørgsel til databassen og gem resultat i variablen $result
				/*$result = mysqli_query($link, $query) or die( 'Der er fejl i forespørgsel, linje: ' . __LINE__ . ' i filen: ' . __FILE__ . '<pre>Forespørgsel: ' . $query . '</pre> Databasefejl: ' . mysqli_error($link) );*/
				?>
				<select name="kategori" required>
					<option value="" hidden>Vælg kategori...</option>
					<?php
					// Brug funktion mysqli_fetch_assoc() til at hente data fra vores forespørgsel og returnere det som et assoc array og gem det i variblen $row. Vi bruger while-løkke til at løbe igennem alle rækker af designere
					while( $row = mysqli_fetch_assoc($result) )
					{
						?>
						<option value="<?php echo $row['kategori_id'] ?>"><?php echo  $row['kategori_navn'] ?></option>
						<?php
					}
					?>
				</select>
			</label>
		</p>

		<?php
		// Hvis der står status i vores URL parametre, køres dette kode
		if ( isset($_GET['status']) )
		{
			// Hvis værdien af status er lig success, vises denne besked
			if ($_GET['status'] == 'success')
			{
				echo '<p>Produktet blev oprettet!</p>';
			}
		}
		?>
		<button type="submit">Opret produkt</button>
	</form>
</body>
</html>
<?php
// Inkludér fil, der lukker forbindelsen til databasen og tømmer vores output buffer
require 'db_close.php';