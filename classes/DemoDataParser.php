<?php


namespace CodingExerciseWithReviews\classes;


/**
 * It is a demo parser for mocking the review REST API
 * Class DemoDataParser
 * @package CodingExerciseWithReviews\classes
 */
class DemoDataParser implements IDataParser
{
    public function fetchUrl($endpointUrl)
    {
        return file_get_contents($endpointUrl);
    }
}