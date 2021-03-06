<?php
// Inkludér fil der etablerer forbindelsen til databasen
require 'db_config.php';

// Hvis URL parametret id er defineret i adresselinjen, så kør dette kode mellem {}
if ( isset($_GET['id']) )
{
	// Hent værdien af URL parametret id og gem i variablen $id
	$id = intval($_GET['id']);

	// Forespørgsel til at hente filnavne fra databasen til de billeder der tilhører det valgte produkt
	$query =
		"SELECT
			produkt_billede_filnavn
		FROM
			produkt_billeder
		WHERE
			fk_produkt_id = $id";

	// Send forespørgsel til databassen og gem resultat i variablen $result
	$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

	// Brug funktion mysqli_fetch_assoc() til at hente data fra vores forespørgsel og returnere det som et assoc array og gem det i variblen $row. Vi bruger while-løkke til at løbe igennem alle rækker af designere
	while( $row = mysqli_fetch_assoc($result) )
	{
		// Tjek at filen eksisterer før vi prøver at slette den med unlink(), for at undgå fejl, hvis ikke filen findes
		if ( file_exists('img/' . $row['produkt_billede_filnavn']) )
		{
			// Slet det store billede fra img-mappen
			unlink('img/' . $row['produkt_billede_filnavn']);
		}

		// Tjek at filen eksisterer før vi prøver at slette den med unlink(), for at undgå fejl, hvis ikke filen findes
		if ( file_exists('img/thumb/' . $row['produkt_billede_filnavn']) )
		{
			// Slet miniaturen fra thumb-mappen
			unlink('img/thumb/' . $row['produkt_billede_filnavn']);
		}
	}

	// Forespørgsel til at slette billedet fra databasen
	// Vi har valgt CASCADE da vi oprettede relationen mellem produkt og dets billeder, derfor sletter den automatisk billederne til det valgte fra databasen
	/*$query =
		"DELETE FROM
			produkt_billeder
		WHERE
			fk_produkt_id = $id";

	// Send forespørgsel til databassen og gem resultat i variablen $result
	$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);*/

	// Forespørgslen til at slette produktet fra databasen, som matcher nummeret gemt i variablen $id
	$query =
		"DELETE FROM
			produkter
		WHERE
			produkt_id = $id";

	// Send forespørgsel til databassen og gem resultat i variablen $result
	$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

	// Opdatér siden for at fjerne URL parametret id, der blev brugt til at slette produktet
	// BEMÆRK: Hvis der er fejl, udkommenter denne header(), da vi ellers ikke kan se evt. fejlbeskeder
	header('Location: index.php?status=success');
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Oversigt over produkter</title>
</head>
<body>
	<h1>CRUD Produkter</h1>

	<h2>Oversigt over produkter</h2>

	<?php
	// Hvis der står status i vores URL parametre, køres dette kode
	if ( isset($_GET['status']) )
	{
		// Hvis værdien af status er lig success, vises denne besked
		if ($_GET['status'] == 'success')
		{
			echo '<p>Produktet blev slettet! <a href="index.php">Luk</a></p>';
		}
	}

	// Hent alle produkter fra databasen or sorter dem efter varenummer
	$query =
		"SELECT
			produkt_id, produkt_navn, produkt_pris, produkt_vare_nr
		FROM
			produkter
		ORDER BY
			produkt_vare_nr";

	// Send forespørgsel til databassen og gem resultat i variablen $result
	$result = mysqli_query($link, $query) or sql_error($query, __LINE__, __FILE__);

	// Hvis der ikke blev fundet nogle rækker/produkter i vores forespørgsel, vises denne besked
	if ( mysqli_num_rows($result) == 0 )
	{
		echo '<p>Der blev ikke fundet nogle produkter, klik <a href="opret-produkt.php">her</a> for at oprette et nyt produkt</p>';
	}
	// Hvis der blev fundet nogle produkter, så vises de her
	else
	{
		?>
		<table cellpadding="4">
			<thead>
			<tr>
				<th>ID</th>

				<th>Vare nr.</th>

				<th>Navn</th>

				<th>Pris</th>

				<th></th> <!-- Tom kolonne til Billede link -->

				<th></th> <!-- Tom kolonne til Rediger link -->

				<th>
					<a href="opret-produkt.php">Opret</a>
				</th>
			</tr>
			</thead>

			<tbody>
			<?php
			// Brug funktion mysqli_fetch_assoc() til at hente data fra vores forespørgsel og returnere det som et assoc array og gem det i variblen $row. Vi bruger while-løkke til at løbe igennem alle rækker af designere
			while( $row = mysqli_fetch_assoc($result) )
			{
				?>
				<tr>
					<td>
						<?php echo $row['produkt_id'] ?>
					</td>

					<td>
						<?php echo $row['produkt_vare_nr'] ?>
					</td>

					<td>
						<?php echo $row['produkt_navn'] ?>
					</td>

					<td>
						<?php echo number_format($row['produkt_pris'], 0, ',', '') // Brug number_format() til at vise produktets pris med 0 decimaler og ingen tusind separator ?> kr.
					</td>

					<td>
						<a href="produkt-billeder.php?id=<?php echo $row['produkt_id'] ?>">Billeder</a>
					</td>

					<td>
						<a href="rediger-produkt.php?id=<?php echo $row['produkt_id'] ?>">Rediger</a>
					</td>

					<td>
						<a href="index.php?id=<?php echo $row['produkt_id'] ?>" onclick="return confirm('Er du sikker på du vil slette produktet og tilhørende billeder?')">Slet</a>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>

		</table>
		<?php
	}
	?>
</body>
</html>
<?php
// Inkludér fil, der lukker forbindelsen til databasen og tømmer vores output buffer
require 'db_close.php';