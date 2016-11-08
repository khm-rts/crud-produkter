<?php
// Inkludér fil der etablerer forbindelsen til databasen
require 'db_config.php';
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
		echo '<p>Der blev ikke fundet nogle produkter</p>';
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

				<th></th>

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
						<a href="rediger-produkt.php?id=<?php echo $row['produkt_id'] ?>">Rediger</a>
					</td>

					<td>
						<a href="index.php?id=<?php echo $row['produkt_id'] ?>">Slet</a>
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