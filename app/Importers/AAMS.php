<?php

namespace App\Importers;

use App\Models\VenueImport;

class AAMS extends Importer
{
	/**
	 * The brand name.
	 *
	 * @var string
	 */
	protected $brand = 'AAMS';

	/**
	 * The brand to use for creating Venue imports.
	 *
	 * @var integer
	 */
	protected $venueImportBrand = VenueImport::SOURCE_BRAND_AAMS;

	/**
	 * The url for requests.
	 *
	 * @var string
	 */
	private $url = 'https://www.agenziadoganemonopoli.gov.it/portale/monopoli/giochi/apparecchi_intr/elenco_soggetti_ries';

	/**
	 * The default params for requests.
	 *
	 * @var array
	 */
	private $params = [
		'p_p_id' => 'webdisplayaams_WAR_webdisplayaamsportlet',
		'p_p_lifecycle' => 2,
		'p_p_state' => 'normal',
		'p_p_mode' => 'view',
		'p_p_cacheability' => 'cacheLevelPage',
		'p_p_col_id' => 'colonna1',
		'p_p_col_pos' => 1,
		'p_p_col_count' => 2,
		'_webdisplayaams_WAR_webdisplayaamsportlet_prov' => '--', // Provincia
		'_webdisplayaams_WAR_webdisplayaamsportlet_el' => 2, // Ricerca per esercizi
		'_webdisplayaams_WAR_webdisplayaamsportlet_anno' => 0, // Anno
		'_webdisplayaams_WAR_webdisplayaamsportlet_comune' => 0, // Comune
		'_webdisplayaams_WAR_webdisplayaamsportlet_tipo_app' => 'A/B', // Tipo (A, B, A/B) - commenta per scaricarli tutti
		'_webdisplayaams_WAR_webdisplayaamsportlet_richiesta' => 1 // Non so a che serve se non a far apparire il pager
	];

	/**
	 * The list of province to iterate on.
	 *
	 * @var array
	 */
	private $province = [];

	public function __construct()
	{
		parent::__construct();

		// Load province
		$paramsStr = http_build_query($this->params);
		$crawler = $this->browser->request('GET', "{$this->url}?{$paramsStr}");

		// Cerco la select con le province e le salvo
		$crawler->filter('#provincia > option')->each(function($option) {
			$value = $option->attr('value');

			if ($value != '0') $this->province[] = $value;
		});
	}

	/**
	 * Load data from the source.
	 *
	 * @return void
	 */
	public function fetch()
	{
		$rows = [];

		foreach ($this->province as $provincia) {
			// Load the page for this provincia
			$params = $this->params;
			$params['_webdisplayaams_WAR_webdisplayaamsportlet_prov'] = $provincia;
			$paramsStr = http_build_query($params);
			$crawler = $this->browser->request('GET', "{$this->url}?{$paramsStr}");

			// Find the number of pages to crawl for this provincia
			$pages = 1;
			$crawler->filter('.num_pagine > p:first-child')->each(function($node, $i) use (&$pages){
				if ($i > 0) return;
				$pages = filter_var($node->text(), FILTER_SANITIZE_NUMBER_INT);
			});

			// Cycle through every page for this provincia
			for ($i = 1; $i <= $pages; $i++) {
				echo "Provincia: {$provincia} - Page {$i} of {$pages}...\n";

				$params['pagina'] = $i;
				$paramsStr = http_build_query($params);
				$crawler = $this->browser->request('GET', "{$this->url}?{$paramsStr}");

				// Find table rows
				$tableRows = $crawler->filter('.tabella_d > tr');

				// Cycle through rows
				$tableRows->each(function($tableRow) use (&$rows) {
					$tableCells = $tableRow->children();

					// Skip header row
					if ($tableCells->first()->nodeName() == 'th') return;

					// Extract cell values
					$tableCellValues = $tableCells->extract(['_text']);

					// Build the row
					$row = new \stdClass();
					$row->codice_censimento_esercizio = $tableCellValues[0];
					$row->denominazione = $tableCellValues[1];
					$row->indirizzo = $tableCellValues[2];
					$row->comune_e_provincia = $tableCellValues[3];
					$row->tipologia_esercizio = $tableCellValues[4];
					$row->superficie_del_locale_in_mq = $tableCellValues[5];
					$row->codice_iscrizione_oggetto = $tableCellValues[6];
					$row->tipologia_apparecchio = $tableCellValues[7];

					$rows[] = $row;
				});
			}
		}

		// Mark as ended
		$this->end();

		return $rows;
	}

	/**
	 * Get the key to retrieve the unique venue id in each data row.
	 *
	 * @return string
	 */
	public function getIdKey()
	{
		return 'codice_censimento_esercizio';
	}

	/**
	 * Get a textual representation for the specified item.
	 *
	 * @param  \stdClass $item
	 * @return string
	 */
	public function getDescriptionForItem(\stdClass $item)
	{
		return "{$item->denominazione}, {$item->indirizzo}, {$item->comune_e_provincia}";
	}

	/**
	 * Normalize source item data for venue creation usage.
	 *
	 * @param  \stdClass $item
	 * @return \stdClass
	 */
	public function normalizeItem(\stdClass $item)
	{
		// Find city
		$city = trim(substr($item->comune_e_provincia, 0, strrpos($item->comune_e_provincia, '(')));

		// Find category
		$categories = [];

		switch ($item->tipologia_esercizio) {
			case 'AGENZIA SCOMMESSE':
			case 'NEGOZIO DI GIOCO':
				$categories[] = [
					'machine_name' => 'betting_shop',
					'is_primary' => true
				];
				break;
			case 'SALA BINGO':
				$categories[] = [
					'machine_name' => 'bingo',
					'is_primary' => true
				];
				break;
			case 'ESERCIZIO DEDICATO VLT/SLOT':
			case 'SALA GIOCHI':
				$categories[] = [
					'machine_name' => 'vlt',
					'is_primary' => true
				];
				break;
		}

		return (object) [
			'aams_census_code' => $item->codice_censimento_esercizio,
			'aams_subject_enrollment_code' => $item->codice_iscrizione_oggetto,
			'name' => $item->denominazione,
			'country' => 'IT',
			'surface_size' => $item->superficie_del_locale_in_mq,
			'categories' => $categories
		];
	}
}