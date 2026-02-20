<?php
/**
 * Class ContentIntegrationTest
 *
 * Tests for equation content integration with WordPress posts.
 * Sample content sourced from ecademy.claude database.
 *
 * @package Equation_Editor
 */

class ContentIntegrationTest extends WP_UnitTestCase {

	/**
	 * Editor user ID.
	 *
	 * @var int
	 */
	private $editor_user_id;

	/**
	 * Set up before each test.
	 */
	public function set_up() {
		parent::set_up();
		$this->editor_user_id = $this->factory->user->create( array( 'role' => 'editor' ) );
		wp_set_current_user( $this->editor_user_id );
	}

	/**
	 * Clean up after each test.
	 */
	public function tear_down() {
		parent::tear_down();
		wp_set_current_user( 0 );
	}

	/**
	 * Sample CodeCogs LaTeX equations from production usage.
	 * These are real formulas found in the ecademy.claude database.
	 *
	 * @return array
	 */
	public function codecogs_latex_samples(): array {
		return array(
			'delta_symbol' => array(
				'latex'       => '\\Delta',
				'description' => 'Greek Delta symbol for change/difference',
				'img_tag'     => '<img src="http://latex.codecogs.com/gif.latex?\\Delta" alt="\\Delta" align="absmiddle" />',
			),
			'mtb_formula_fr' => array(
				'latex'       => '\\fn_phv \\large ((ROCE*(1-taux impôts)-g))/((CMPC-g) )',
				'description' => 'Market-To-Book formula (French)',
				'img_tag'     => '<img src="http://latex.codecogs.com/gif.latex?\\fn_phv&space;\\large&space;((ROCE*(1-taux&space;impôts)-g))/((CMPC-g)&space;)" alt="\\fn_phv \\large ((ROCE*(1-taux impôts)-g))/((CMPC-g) )" align="absmiddle" />',
			),
			'mtb_formula_en' => array(
				'latex'       => '\\fn_phv \\large ((ROCE*(1-tax rate)-g))/((WACC-g) )',
				'description' => 'Market-To-Book formula (English)',
				'img_tag'     => '<img src="http://latex.codecogs.com/gif.latex?\\fn_phv&space;\\large&space;((ROCE*(1-tax&space;rate)-g))/((WACC-g)&space;)" alt="\\fn_phv \\large ((ROCE*(1-tax rate)-g))/((WACC-g) )" align="absmiddle" />',
			),
			'tobins_q_formula' => array(
				'latex'       => 'g =(Tobin\'s Q x WACC – ROCE x (1-Tax ratio))/(Tobin\'s Q-1)',
				'description' => 'Tobin\'s Q implicit growth formula',
				'img_tag'     => '<img src="https://latex.codecogs.com/gif.latex?g&space;=(Tobin\'s&space;Q&space;x&space;WACC&space;–&space;ROCE&space;x&space;(1-Tax&space;ratio))/(Tobin\'s&space;Q-1)" alt="g =(Tobin\'s Q x WACC – ROCE x (1-Tax ratio))/(Tobin\'s Q-1)" align="absmiddle" />',
			),
		);
	}

	/**
	 * Test that posts with simple Delta equation can be created and retrieved.
	 */
	public function test_post_with_delta_equation() {
		$samples = $this->codecogs_latex_samples();
		$content = 'Calculez le Free Cash-Flow (pour le calcul de ' . $samples['delta_symbol']['img_tag'] . 'BFR)';

		$post_id = $this->factory->post->create( array(
			'post_content' => $content,
			'post_status'  => 'publish',
		) );

		$post = get_post( $post_id );
		$this->assertStringContainsString( 'latex.codecogs.com', $post->post_content );
		// Note: WordPress strips backslashes, so \Delta becomes Delta
		$this->assertStringContainsString( 'Delta', $post->post_content );
	}

	/**
	 * Test that posts with MTB formula (French) can be created and retrieved.
	 */
	public function test_post_with_mtb_formula_french() {
		$samples = $this->codecogs_latex_samples();
		$content = '<h4><strong>MTB =</strong> ' . $samples['mtb_formula_fr']['img_tag'] . '</h4>';

		$post_id = $this->factory->post->create( array(
			'post_content' => $content,
			'post_status'  => 'publish',
		) );

		$post = get_post( $post_id );
		$this->assertStringContainsString( 'ROCE', $post->post_content );
		$this->assertStringContainsString( 'CMPC', $post->post_content );
		$this->assertStringContainsString( 'latex.codecogs.com', $post->post_content );
	}

	/**
	 * Test that posts with MTB formula (English) can be created and retrieved.
	 */
	public function test_post_with_mtb_formula_english() {
		$samples = $this->codecogs_latex_samples();
		$content = '<h4><strong>MTB =</strong> ' . $samples['mtb_formula_en']['img_tag'] . '</h4>';

		$post_id = $this->factory->post->create( array(
			'post_content' => $content,
			'post_status'  => 'publish',
		) );

		$post = get_post( $post_id );
		$this->assertStringContainsString( 'WACC', $post->post_content );
		$this->assertStringContainsString( 'tax rate', $post->post_content );
	}

	/**
	 * Test that posts with Tobin's Q formula can be created and retrieved.
	 */
	public function test_post_with_tobins_q_formula() {
		$samples = $this->codecogs_latex_samples();
		$content = 'The implicit free-cash-flow growth (g) is calculated as follows: ' . $samples['tobins_q_formula']['img_tag'];

		$post_id = $this->factory->post->create( array(
			'post_content' => $content,
			'post_status'  => 'publish',
		) );

		$post = get_post( $post_id );
		$this->assertStringContainsString( 'Tobin', $post->post_content );
		$this->assertStringContainsString( 'Tax ratio', $post->post_content );
	}

	/**
	 * Test post content with multiple equations (production-like content).
	 */
	public function test_post_with_multiple_equations() {
		$samples = $this->codecogs_latex_samples();

		$content = 'Utilisons maintenant la formule :

FCF = EBITDA *(1 – Tis) + Tis * Amortissements – Investissements - ' . $samples['delta_symbol']['img_tag'] . 'BFR

Calculez le FCF avec le même niveau d\'investissements.

Le ratio Market-To-Book se calcule ainsi :

<h4><strong>MTB =</strong> ' . $samples['mtb_formula_fr']['img_tag'] . '</h4>';

		$post_id = $this->factory->post->create( array(
			'post_content' => $content,
			'post_status'  => 'publish',
		) );

		$post = get_post( $post_id );

		// Verify both equations are present
		// Note: WordPress strips backslashes, so \Delta becomes Delta
		$this->assertStringContainsString( 'Delta', $post->post_content );
		$this->assertStringContainsString( 'CMPC', $post->post_content );

		// Count img tags with codecogs
		$img_count = substr_count( $post->post_content, 'latex.codecogs.com' );
		$this->assertEquals( 2, $img_count );
	}

	/**
	 * Test that equation img tags survive WordPress content filtering.
	 */
	public function test_equation_survives_content_filter() {
		$samples  = $this->codecogs_latex_samples();
		$img_tag  = $samples['delta_symbol']['img_tag'];
		$content  = 'Test content with equation: ' . $img_tag;

		$post_id = $this->factory->post->create( array(
			'post_content' => $content,
			'post_status'  => 'publish',
		) );

		// Get the filtered content as it would appear on the frontend
		$filtered_content = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );

		// The img tag should still be present after filtering
		$this->assertStringContainsString( 'latex.codecogs.com', $filtered_content );
	}

	/**
	 * Test that special characters in LaTeX are preserved.
	 */
	public function test_special_characters_preserved() {
		$samples = $this->codecogs_latex_samples();

		// Tobin's Q contains apostrophe and special characters
		$content = $samples['tobins_q_formula']['img_tag'];

		$post_id = $this->factory->post->create( array(
			'post_content' => $content,
			'post_status'  => 'publish',
		) );

		$post = get_post( $post_id );

		// Check special characters are preserved
		$this->assertStringContainsString( 'Tobin\'s', $post->post_content );
		$this->assertStringContainsString( '–', $post->post_content ); // en-dash
	}

	/**
	 * Test that unicode characters (French accents) are preserved.
	 */
	public function test_unicode_characters_preserved() {
		$samples = $this->codecogs_latex_samples();

		// French formula contains "impôts" with circumflex
		$content = $samples['mtb_formula_fr']['img_tag'];

		$post_id = $this->factory->post->create( array(
			'post_content' => $content,
			'post_status'  => 'publish',
		) );

		$post = get_post( $post_id );

		// Check French characters are preserved in the alt attribute
		$this->assertStringContainsString( 'impôts', $post->post_content );
	}

	/**
	 * Test updating a post with equation content.
	 */
	public function test_update_post_with_equation() {
		$samples = $this->codecogs_latex_samples();

		// Create post with one equation
		$post_id = $this->factory->post->create( array(
			'post_content' => 'Initial: ' . $samples['delta_symbol']['img_tag'],
			'post_status'  => 'publish',
		) );

		// Update with different equation
		wp_update_post( array(
			'ID'           => $post_id,
			'post_content' => 'Updated: ' . $samples['mtb_formula_en']['img_tag'],
		) );

		$post = get_post( $post_id );

		// Old equation should be gone (using alt text to check)
		$this->assertStringNotContainsString( 'alt="Delta"', $post->post_content );

		// New equation should be present
		$this->assertStringContainsString( 'WACC', $post->post_content );
	}

	/**
	 * Test searching for posts with equation content.
	 */
	public function test_search_posts_with_equations() {
		$samples = $this->codecogs_latex_samples();

		// Create a post with equation
		$this->factory->post->create( array(
			'post_title'   => 'Financial Analysis',
			'post_content' => 'The MTB formula is: ' . $samples['mtb_formula_en']['img_tag'],
			'post_status'  => 'publish',
		) );

		// Create a post without equation
		$this->factory->post->create( array(
			'post_title'   => 'Regular Post',
			'post_content' => 'This is just regular content.',
			'post_status'  => 'publish',
		) );

		// Search for posts containing codecogs
		$query = new WP_Query( array(
			's' => 'WACC',
		) );

		$this->assertGreaterThanOrEqual( 1, $query->found_posts );
	}

	/**
	 * Test post excerpt with equation content.
	 */
	public function test_excerpt_with_equation() {
		$samples = $this->codecogs_latex_samples();

		$content = 'This post explains the Market-To-Book ratio: ' . $samples['mtb_formula_en']['img_tag'] . ' which is used in financial analysis.';

		$post_id = $this->factory->post->create( array(
			'post_content' => $content,
			'post_status'  => 'publish',
		) );

		// Get auto-generated excerpt
		$excerpt = get_the_excerpt( $post_id );

		// Excerpt should have some content (img may or may not be stripped depending on WP version)
		$this->assertNotEmpty( $excerpt );
	}

	/**
	 * Test that both HTTP and HTTPS CodeCogs URLs work.
	 */
	public function test_http_and_https_codecogs_urls() {
		$http_img  = '<img src="http://latex.codecogs.com/gif.latex?\\Delta" alt="\\Delta" />';
		$https_img = '<img src="https://latex.codecogs.com/gif.latex?\\Delta" alt="\\Delta" />';

		$post_id_http = $this->factory->post->create( array(
			'post_content' => 'HTTP: ' . $http_img,
			'post_status'  => 'publish',
		) );

		$post_id_https = $this->factory->post->create( array(
			'post_content' => 'HTTPS: ' . $https_img,
			'post_status'  => 'publish',
		) );

		$post_http  = get_post( $post_id_http );
		$post_https = get_post( $post_id_https );

		$this->assertStringContainsString( 'http://latex.codecogs.com', $post_http->post_content );
		$this->assertStringContainsString( 'https://latex.codecogs.com', $post_https->post_content );
	}

	/**
	 * Test equation in post with complex HTML structure.
	 */
	public function test_equation_in_complex_html() {
		$samples = $this->codecogs_latex_samples();

		$content = '<div class="article-content">
<h2>Financial Formulas</h2>
<p>The Market-To-Book (MTB) ratio is calculated as:</p>
<blockquote>
<h4><strong>MTB =</strong> ' . $samples['mtb_formula_en']['img_tag'] . '</h4>
</blockquote>
<p>Where g represents the long-term growth of free cash flows.</p>
<table>
<tr><td>Variable</td><td>Description</td></tr>
<tr><td>ROCE</td><td>Return on Capital Employed</td></tr>
<tr><td>WACC</td><td>Weighted Average Cost of Capital</td></tr>
</table>
</div>';

		$post_id = $this->factory->post->create( array(
			'post_content' => $content,
			'post_status'  => 'publish',
		) );

		$post = get_post( $post_id );

		// Verify HTML structure and equation are preserved
		$this->assertStringContainsString( '<blockquote>', $post->post_content );
		$this->assertStringContainsString( '<table>', $post->post_content );
		$this->assertStringContainsString( 'latex.codecogs.com', $post->post_content );
	}
}
