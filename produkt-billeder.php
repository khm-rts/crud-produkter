<?php
// Inkludér fil der etablerer forbindelsen til databasen
require 'db_config.php';
// Inkludér WideImage biblioteket, som skal bruges til at tilpasse størrelse på billeder
require 'wideimage-11.02.19-lib/WideImage.php';

// Hvis id er defineret i vores URL parametre, køres dette kode mellem {}
if ( isset($_GET['id']) )
{
	// Hent værdien af URL parametret id og gem i variablen $id
	$id		= intval($_GET['id']);

	// Hent produktet fra databasen, der matcher produkt_id gemt i variablen $id
	$query	=
		"SELECT
			produkt_navn
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

	// Hvis formularen er sendt og der er valgt et billede, køres koden mellem {}
	if ( isset($_FILES['billede']['tmp_name']) )
	{
		$filnavn = $_FILES['billede']['name'];
	}
}
else
{
	die('Der er ikke valgt et produkt. (Der står ikke ?id=et-tal i adresselinjen)');
}

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

	<!-- HUSK enctype="multipart/form-data", ellers kan der ikke uploades filer -->
	<form method="post" enctype="multipart/form-data">
		<p>
			<label>
				Billede:
				<input type="file" name="billede" required accept="image/*">
			</label>
		</p>

		<button type="submit">Upload</button>
	</form>
</body>
</html>
<?php
// Inkludér fil, der lukker forbindelsen til databasen og tømmer vores output buffer
require 'db_close.php';