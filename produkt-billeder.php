<?php
// Inkludér fil der etablerer forbindelsen til databasen
require 'db_config.php';
// Inkludér WideImage biblioteket, som skal bruges til at tilpasse størrelse på billeder
require 'wideimage-11.02.19-lib/WideImage.php';

// Hvis id er defineret i vores URL parametre, køres dette kode mellem {}
if ( isset($_GET['id']) )
{
	// Hent værdien af URL parametret id og gem i variablen $id
	$produkt_id		= intval($_GET['id']);

	// Hent produktet fra databasen, der matcher produkt_id gemt i variablen $id
	$query	=
		"SELECT
			produkt_navn
		FROM
			produkter
		WHERE
			produkt_id = $produkt_id";

	// Send forespørgsel til databassen og gem resultat i variablen $result
	$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

	// Brug funktionen mysqli_num_rows() til at returnere antallet af rækker (produkter) fra vores forespørgsel og hvis der blev fundet 0 produkter, vises denne besked
	if ( mysqli_num_rows($result) == 0 )
	{
		die('Der blev ikke fundet et produkt i databasen, der matcher id: ' . $produkt_id );
	}

	// Brug funktionen mysqli_fetch_assoc() til at hente dataen fra vores forespørgsel, og gemme som et assoc array (Hvor kolonnenavnene fra databasen, bruges som index/key i array) i variablen $produkt
	$produkt	= mysqli_fetch_assoc($result);

	// Hvis formularen er sendt og der er valgt et billede, køres koden mellem {}
	if ( isset($_FILES['billede']['tmp_name']) )
	{
		// Vi gemmer billedets oprindelige filnavn og escaper navnet for at sikre imod SQL injections når variablen bruges i vores INSERT-sætning
		$filnavn = mysqli_real_escape_string($link, $_FILES['billede']['name']);

		// For at sikre at vi ikke får identiske filnavne, tilføjer vi et unix timestamp og et _ foran det oprindelige filnavn. Et unix timestamp får man ved at kalde funktionen time() og indeholder et tal for hvore mange sekunder der er gået siden d. 1970-01-01
		$filnavn = time() . '_' . $filnavn;

		// Hent det uploadede billede fra input med name 'billede' vha. den statiske metode load i klassen WideImage og gem instansen i variablen $img
		$img = WideImage::load('billede');

		// Brug metoden resize på vores instans af billedet med parametrene 1920, som er den maximale bredde, samt 1080, som er den maksimale højde vi ønsker. Så gemmer vi det tilpassede billede i en ny variabel vi har kaldt $resized_img
		$resized_img = $img->resize(1920, 1080);

		// Brug metoden saveToFile på vores variabel $resized_img, som indeholder det tilpassede billede og angiv stien til hvor billedet skal gemmes som parameter.
		$resized_img->saveToFile('img/' . $filnavn);

		// Brug metode resizeDown på vores instans af billedet med parametre 240 som maximal bredde og 135 som maksimal højde. Det er størrelser som stadig svarer til 16:9 formatet, ligesom FullHD: 1920x1080. Vi bruger resizeDown i stedet for resize, så billedet kun nedskaleres, hvis det er større end 240x135
		$thumb_img = $img->resizeDown(240, 135);

		$thumb_img->saveToFile('img/thumb/' . $filnavn);

		// Forespørgsel til at oprette billedet i databasen
		$query =
			"INSERT INTO
				produkt_billeder (produkt_billede_filnavn, fk_produkt_id)
			VALUES
				('$filnavn', $produkt_id)";

		// Send forespørgsel til databassen og gem resultat i variablen $result
		$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

		// Opdatér siden og tilføj URL parametret status med værdien success, som bruges til at vise status besked ved submit-knappen.
		// BEMÆRK: Hvis der er fejl, udkommenter denne header(), da vi ellers ikke kan se evt. fejlbeskeder
		header('Location: produkt-billeder.php?id=' . $produkt_id . '&status=success');
	}
}
else
{
	die('Der er ikke valgt et produkt. (Der står ikke ?id=et-tal i adresselinjen)');
}

// Fejlsøg med et print på $_FILES
echo '<pre>';
print_r($_FILES);
echo '</pre>';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Produktbilleder</title>
</head>
<body>
	<h1>CRUD produkter</h1>

	<h2>Produktbilleder til <?php echo $produkt['produkt_navn'] ?></h2>

	<?php
	// Forespørgsel til at hente billeder fra databasen til det valgte produkt or sortere dem med primært billede først
	$query =
		"SELECT
			*
		FROM
			produkt_billeder
		WHERE
			fk_produkt_id = $produkt_id
		ORDER BY 
			produkt_billede_er_primaer DESC";

	// Send forespørgsel til databassen og gem resultat i variablen $result
	$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

	// Brug funktion mysqli_fetch_assoc() til at hente data fra vores forespørgsel og returnere det som et assoc array og gem det i variblen $row. Vi bruger while-løkke til at løbe igennem alle rækker af designere
	while( $row = mysqli_fetch_assoc($result) )
	{
		// Vis billederne fra databasen
		?>
		<img src="img/thumb/<?php echo $row['produkt_billede_filnavn'] ?>">
		<?php
	}
	?>

	<!-- HUSK enctype="multipart/form-data", ellers kan der ikke uploades filer -->
	<form method="post" enctype="multipart/form-data">
		<p>
			<label>
				Billede:
				<input type="file" name="billede" required accept="image/*">
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
				<p>Billedet blev uploadet! <a href="produkt-billeder.php?id=<?php echo $produkt_id ?>">Luk</a> eller klik <a href="index.php">her</a> for at gå tilbage til oversigt</p>
				<?php
			}
		}
		?>
		<button type="submit">Upload</button>
	</form>
</body>
</html>
<?php
// Inkludér fil, der lukker forbindelsen til databasen og tømmer vores output buffer
require 'db_close.php';