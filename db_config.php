<?php
// Start output buffer vha. funktionen ob_start(), for at forhindre warnings, f.eks. "cannot modify header information"
ob_start();

$db_host	= 'localhost';
$db_user	= 'root';
$db_pass	= '';
$db_name	= 'crud_produkter';

// Opretter forbindelse til databasen og gemmer den i variablen $link
$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Hvis forbindelsen til databasen fejler, udskriv fejlbesked
if (!$link)
{
	die('Forbindelsen til databasen fejlede: ' . mysqli_error($link) );
}

// Sæt tegnsætning til utf8, så bl.a. æøå understørres
mysqli_set_charset($link, 'utf8');

/**
 * Funktion til at vise fejlsøgningsinfo når der sendes forespørgsler til databasen
 * @param string $query:	Den forespørgsel der bliver sendt til databasen
 * @param int $line:		Det aktuelle linjenummer, hvor funktion køres, som man får vha. konstanten __LINE__
 * @param string $file:		Den aktuelle fil, hvor funktion køres, som man får vha. konstanten __FILE__
 */
function sql_error($query, $line, $file)
{
	// Gør variablen global, så den er tilgængelig inde i funktion
	global $link;

	// Brug die, for at stoppe videre indlæsning af fil og vis besked i parantesen
	die( 'Der er fejl i forespørgsel, linje: ' . $line . ' i filen: ' . $file . '<pre>Forespørgsel: ' . $query . '</pre> Databasefejl: ' . mysqli_error($link) );
}