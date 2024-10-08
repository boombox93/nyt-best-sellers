<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class BestSellersTest extends TestCase
{
    private string $endpoint;

    /**
     * Set private properties to be used in test cases
     *
     * @return void
     */
    protected function setup() : void
    {
        parent::setup();
        $this->endpoint = '/api/'.env('API_VERSION').'/nyt/best-sellers';
    }

    /**
     * Test case for when no params are submitted to the NYT best-sellers endpoint
     *
     * @return void
     */
    public function test_best_sellers_no_params()
    {
        Http::fake([
            '*' => Http::response([
                'status' => 'OK',
                'results' => [
                    [
                        'title' => 'YOU JUST NEED TO LOSE WEIGHT',
                        'author' => 'Aubrey Gordon',
                    ],
                    [
                        'title' => '#GIRLBOSS',
                        'author' => 'Sophia Amoruso',
                    ],
                ],
            ], 200)
        ]);
        $response = $this->get($this->endpoint);

        $this->assertTrue(
            $response['success'],
            'Assert successful response'
        );

        $this->assertEquals(
            200,
            $response['status'],
            'Assert that the status expected is what was received'
        );

        $this->assertEquals(
            'Successfully executed the request.',
            $response['message'],
            'Assert that the message expected is what was received'
        );

        $this->assertTrue(
            isset($response['data']),
            'Assert that data is set in the response'
        );

        $this->assertEquals(
            'OK',
            $response['data']['status'],
            'Assert that the status received matches what is expected'
        );

        $this->assertNotEmpty($response['data']['results'], 'Assert non empty result set');
    }

    /**
     * Test case for author param to the NYT best-sellers endpoint
     *
     * @return void
     */
    public function test_best_sellers_author_param()
    {
        Http::fake([
            '*' => Http::response([
                'results' => [
                    [
                        'title' => 'A FACE IN THE CROWD',
                        'author' => 'Stephen King',
                    ],
                    [
                        'title' => 'BILLY SUMMERS',
                        'author' => 'Stephen King',
                    ],
                ],
            ], 200)
        ]);

        $author = 'Stephen King';
        $response = $this->json('GET', $this->endpoint, [
           'author' => $author,
        ]);

        $this->assertTrue(
            $response['success'],
            'Assert that there was a successful response'
        );

        $this->assertEquals(
            'Successfully executed the request.',
            $response['message'],
            'Assert that the message expected is what was received'
        );

        $this->assertTrue(
            isset($response['data']),
            'Assert that data is set in the response'
        );

        $this->assertNotEmpty(
            $response['data']['results'],
            'Assert non empty result set'
        );

        $jsonData = $response->json('data');
        $this->assertEquals(                                                                                            // Assert that the author returned in the results is the author we filtered by
            $author,
            $jsonData['results'][0]['author'],
            'Assert that the author received matches the author being filtered'
        );
    }

    /**
     * Test case for tile param to the NYT best-sellers endpoint
     *
     * @return void
     */
    public function test_best_sellers_title_param()
    {
        $title = 'FOURTH WING';
        Http::fake([
            '*' => Http::response([
                'results' => [
                    [
                        'title' => 'FOURTH WING',
                        'author' => 'Rebecca Yarros',
                    ],
                ],
            ], 200)
        ]);
        $response = $this->json('GET', $this->endpoint, [
            'title' => $title,
        ]);

        $this->assertTrue(
            $response['success'],
            'Assert that there was a successful response'
        );

        $this->assertEquals(
            'Successfully executed the request.',
            $response['message'],
            'Assert that the message expected is what was received'
        );

        $this->assertTrue(
            isset($response['data']),
            'Assert that data is set in the response'
        );

        $this->assertNotEmpty(
            $response['data']['results'],
            'Assert non empty result set'
        );

        $jsonData = $response->json('data');
        $this->assertEquals(                                                                                            // Assert that the title returned in the results is the title we filtered by
            $title,
            $jsonData['results'][0]['title'],
            'Assert that the title received matches the title being filtered'
        );
    }

    /**
     * Test case for isbn param to the NYT best-sellers endpoint
     *
     * @return void
     */
    public function test_best_sellers_isbn_param()
    {
        $isbns = [
            1649374046,
            9781649374042
        ];
        $title = 'FOURTH WING';
        Http::fake([
            '*' => Http::response([
                'results' => [
                    [
                        'title' => 'FOURTH WING',
                        'author' => 'Rebecca Yarros',
                    ],
                ],
            ], 200)
        ]);
        $response = $this->json('GET', $this->endpoint, [
            'isbn' => $isbns,
        ]);

        $this->assertTrue(
            $response['success'],
            'Assert that there was a successful response'
        );

        $this->assertEquals(
            'Successfully executed the request.',
            $response['message'],
            'Assert that the message expected is what was received'
        );

        $this->assertTrue(
            isset($response['data']),
            'Assert that data is set in the response'
        );

        $this->assertNotEmpty(
            $response['data']['results'],
            'Assert non empty result set'
        );

        $jsonData = $response->json('data');
        $this->assertEquals(                                                                                            // Assert that the author returned in the results is the author we filtered by
            $title,
            $jsonData['results'][0]['title'],
            'Assert that the title received matches the isbn being filtered'
        );
    }

    /**
     * Test case for offset param to the NYT best-sellers endpoint
     *
     * @return void
     */
    public function test_best_sellers_offset_param()
    {
        Http::fake([
            '*' => Http::response([
                'results' => [
                    [
                        'title' => 'A FACE IN THE CROWD',
                        'author' => 'Stephen King',
                    ],
                    [
                        'title' => 'BILLY SUMMERS',
                        'author' => 'Stephen King',
                    ],
                ],
            ], 200)
        ]);
        $response = $this->get($this->endpoint, [
            'offset' => 20,
        ]);

        $this->assertTrue(
            $response['success'],
            'Assert successful response'
        );

        $this->assertEquals(
            200,
            $response['status'],
            'Assert that the status expected is what was received'
        );

        $this->assertEquals(
            'Successfully executed the request.',
            $response['message'],
            'Assert that the message expected is what was received'
        );

        $this->assertTrue(
            isset($response['data']),
            'Assert that data is set in the response'
        );

        $this->assertNotEmpty(
            $response['data']['results'],
            'Assert non empty result set'
        );
    }

    public function test_best_sellers_all_params() {
        $isbns = [
            1649374046,
            9781649374042
        ];
        Http::fake([
            '*' => Http::response([
                'results' => [
                    [
                        'title' => 'FOURTH WING',
                        'author' => 'Rebecca Yarros',
                    ],
                ],
            ], 200)
        ]);
        $response = $this->json('GET', $this->endpoint, [
            'author' => 'Rebecca Yarros',
            'title' => 'FOURTH WING',
            'isbn' => $isbns,
            'offset' => 40,
        ]);

        $this->assertTrue(
            $response['success'],
            'Assert successful response'
        );

        $this->assertEquals(
            200,
            $response['status'],
            'Assert that the status expected is what was received'
        );

        $this->assertEquals(
            'Successfully executed the request.',
            $response['message'],
            'Assert that the message expected is what was received'
        );

        $this->assertTrue(
            isset($response['data']),
            'Assert that data is set in the response'
        );

        $this->assertNotEmpty(
            $response['data']['results'],
            'Assert non empty result set'
        );
    }

    /**
     * Test case for invalid offset param to be handled by form validation
     *
     * @return void
     */
    public function test_best_sellers_invalid_offset_param()
    {
        // No need to fake since this will be immediately handled by form
        $response = $this->json('GET', $this->endpoint, [
            'offset' => 'string',
        ]);

        $this->assertFalse(
            $response['success'],
            'Assert the response success flag is false',
        );

        $this->assertEquals(
            422,
            $response['status'],
            'Assert that the response status is a 422 response code'
        );

        $this->assertArrayHasKey(
            'error',
            $response,
            'Assert that the form validation encountered errors'
        );

        $this->assertEquals(
            'An invalid parameter was provided.',
            $response['message'],
            'Assert that the request error message is set'
        );

        $this->assertTrue(
            isset($response['error']['offset']),
            'Assert that the error received is due to the offset value'
        );

        $this->assertEquals(
            'The offset field must be an integer.',
            $response['error']['offset'][0],
            'Assert that the offset expected is invalid'
        );
    }

    /**
     * Test case for invalid isbn param to be handled by form validation
     *
     * @return void
     */
    public function test_best_sellers_invalid_isbn_param()
    {
        // No need to fake since this will be immediately handled by form
        $response = $this->json('GET', $this->endpoint, [
            'isbn' => [
                'isbn',
                1234567891234567,
            ],
        ]);

        $this->assertFalse(
            $response['success'],
            'Assert the response success flag is false',
        );

        $this->assertEquals(
            422,
            $response['status'],
            'Assert that the response status is a 422 response code'
        );

        $this->assertArrayHasKey(
            'error',
            $response,
            'Assert that the form validation encountered errors'
        );

        $this->assertEquals(
            'An invalid parameter was provided.',
            $response['message'],
            'Assert that the request error message is set'
        );

        $this->assertTrue(
            isset($response['error']['isbn.0']),
            'Assert that one of the errors received is due to the first offset value'
        );

        $this->assertTrue(
            isset($response['error']['isbn.1']),
            'Assert that one of the errors received is due to the second offset value'
        );

        $this->assertEquals(
            'An ISBN must contain 10 or 13 digits.',
            $response['error']['isbn.0'][0],
            'Assert that the first isbn submitted is invalid'
        );

        $this->assertEquals(
            'An ISBN must contain 10 or 13 digits.',
            $response['error']['isbn.1'][0],
            'Assert that the second isbn submitted is invalid'
        );
    }

    /**
     * Test case for invalid author param to be handled by form validation
     *
     * @return void
     */
    public function test_best_sellers_invalid_author_param()
    {
        // No need to fake since this will be immediately handled by form
        $response = $this->json('GET', $this->endpoint, [
            'author' => 123
        ]);

        $this->assertFalse(
            $response['success'],
            'Assert the response success flag is false',
        );

        $this->assertEquals(
            422,
            $response['status'],
            'Assert that the response status is a 422 response code'
        );

        $this->assertEquals(
            'An invalid parameter was provided.',
            $response['message'],
            'Assert that the request error message is set'
        );

        $this->assertArrayHasKey(
            'error',
            $response,
            'Assert that the form validation encountered errors'
        );

        $this->assertTrue(
            isset($response['error']['author']),
            'Assert that error received is due to the author value'
        );

        $this->assertEquals(
            'The author field must be a string.',
            $response['error']['author'][0],
            'Assert that the author expected is invalid'
        );
    }

    /**
     * Test case for invalid title param to be handled by form validation
     *
     * @return void
     */
    public function test_best_sellers_invalid_title_param()
    {
        // No need to fake since this will be immediately handled by form
        $response = $this->json('GET', $this->endpoint, [
            'title' => 123
        ]);

        $this->assertFalse(
            $response['success'],
            'Assert the response success flag is false',
        );

        $this->assertEquals(
            422,
            $response['status'],
            'Assert that the response status is a 422 response code'
        );

        $this->assertEquals(
            'An invalid parameter was provided.',
            $response['message'],
            'Assert that the request error message is set'
        );

        $this->assertArrayHasKey(
            'error',
            $response,
            'Assert that the form validation encountered errors'
        );

        $this->assertEquals(
            'The title field must be a string.',
            $response['error']['title'][0],
            'Assert that the title expected is invalid'
        );
    }
}
