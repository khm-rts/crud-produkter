<?php
// Inkludér fil der etablerer forbindelsen til databasen
require 'db_config.php';

// Hvis id er defineret i vores URL parametre, køres dette kode mellem {}
if ( isset($_GET['id']) )
{
	// Hent værdien af URL parametret id og gem i variablen $id
	$id		= intval($_GET['id']);

	// Hent produktet fra databasen, der matcher produkt_id gemt i variablen $id
	$query	=
		"SELECT
			*
		FROM
			produkter
		WHERE
			produkt_id = $id";

	// Send forespørgsel til databassen og gem resultat i variablen $result
	$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

	// Brug funktionen mysqli_num_rows() til at returnere antallet af rækker (produkter) fra vores forespørgsel og hvis der blev fundet 0 produkter, vises denne besked
	if ( mysqli_num_rows($result) == 0 )
	{
		die('Der blev ikke fundet et produkt i databasen, der matcher id: ' . $id );
	}

	// Brug funktionen mysqli_fetch_assoc() til at hente dataen fra vores forespørgsel, og gemme som et assoc array (Hvor kolonnenavnene fra databasen, bruges som index/key i array) i variablen $produkt
	$produkt	= mysqli_fetch_assoc($result);
}
else
{
	die('Der er ikke valgt et produkt. (Der står ikke ?id=et-tal i adresselinjen)');
}

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

	// Forespørgsel til aT opdatere produktet i databasen
	$query =
		"UPDATE
			produkter
		SET
			produkt_navn = '$navn', produkt_pris = $pris, produkt_design_aar = $aar, produkt_vare_nr = $varenr, produkt_beskrivelse = '$beskrivelse', fk_designer_id = $designer, fk_kategori_id = $kategori
		WHERE
			produkt_id = $id";

	// Send forespørgsel til databassen og gem resultat i variablen $result
	$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

	// Opdatér siden og tilføj URL parametret status med værdien success, som bruges til at vise status besked ved submit-knappen
	header('Location: rediger-produkt.php?id=' . $id . '&status=success');
	// BEMÆRK: Hvis der er fejl, udkommenter denne header(), da vi ellers ikke kan se evt. fejlbeskeder
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Rediger produkt</title>
</head>
<body>
	<h1>CRUD Produkter</h1>

	<h2>Rediger produkt</h2>

	<hr>

	<form method="post">
		<p>
			<label>
				Navn:
				<input type="text" name="navn" value="<?php echo $produkt['produkt_navn'] ?>" required maxlength="50">
			</label>
		</p>

		<p>
			<label>
				Pris:
				<input type="number" step="0.25" lang="da" name="pris" required min="0" max="999999.99" value="<?php echo $produkt['produkt_pris'] ?>">
			</label>
		</p>

		<p>
			<label>
				Design år:
				<input	type="number" name="aar" required min="1" max="9999" value="<?php echo $produkt['produkt_design_aar'] // Udskriv aktuelt årstal ?>">
			</label>
		</p>

		<p>
			<label>
				Vare nr.:
				<input type="number" name="varenr" id="test" min="100000" max="16777215" required value="<?php echo $produkt['produkt_vare_nr'] ?>">
			</label>
		</p>

		<p>
			<label>
				Beskrivelse:
				<textarea name="beskrivelse" required cols="30" rows="4" style="resize: none"><?php echo $produkt['produkt_beskrivelse'] ?></textarea>
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
						$selected = '';
						// Hvis produktets aktuelle værdi for fk_designer_id, matcher rækken med designer_id, gemmes attributten selected i variablen som vi også har kaldt selected
						if ($produkt['fk_designer_id'] == $row['designer_id'])
						{
							$selected = 'selected';
						}

						// echo '<option value="' . $row['designer_id'] . '" ' . $selected . '>' . $row['designer_navn'] . '</option>';

						// Udskriv designerens id og navn fra databasen
						?>
						<option value="<?php echo $row['designer_id'] ?>" <?php echo $selected ?>>
							<?php echo $row['designer_navn'] ?>
						</option>
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
						$selected = '';
						// Hvis produktets aktuelle værdi for fk_designer_id, matcher rækken med designer_id, gemmes attributten selected i variablen som vi også har kaldt selected
						if ($produkt['fk_kategori_id'] == $row['kategori_id'])
						{
							$selected = 'selected';
						}

						// Ovenstående kan skrives shorthand, hvis man vil spare lidt plads
						// $selected = ($produkt['fk_kategori_id'] == $row['kategori_id']) ? 'selected' : '';
						?>
						<option value="<?php echo $row['kategori_id'] ?>" <?php echo $selected ?>>
							<?php echo $row['kategori_navn'] ?>
						</option>
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
				?>
				<p>Produktet blev rettet! <a href="rediger-produkt.php?id=<?php echo $id ?>">Luk</a> eller klik <a href="index.php">her</a> for at gå tilbage til oversigt</p>
				<?php
			}
		}
		?>
		<button type="submit">Rediger produkt</button>
	</form>
</body>
</html>
<?php
// Inkludér fil, der lukker forbindelsen til databasen og tømmer vores output buffer
require 'db_close.php';