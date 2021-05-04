<?php


namespace CodingExerciseWithReviews\classes;


class ReviewEndpoint
{
    const TOP_LISTS = 'toplists';
    const TOP_LIST_ID = 575;

    /** @var IDataParser */
    private $dataParser;
    private $endpointUrl;

    public function __construct(IDataParser $dataParser, $endpointUrl)
    {
        $this->dataParser = $dataParser;
        $this->endpointUrl = $endpointUrl;
    }

    public function parseReviewResponse()
    {
        $response = $this->dataParser->fetchUrl($this->endpointUrl);
        $data = json_decode($response, true);
        $reviews = !empty($data[self::TOP_LISTS][self::TOP_LIST_ID]) ? $data[self::TOP_LISTS][self::TOP_LIST_ID] : null;

        return $reviews;
    }
}