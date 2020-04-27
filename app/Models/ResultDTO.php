<?php


namespace App\Models;

/**
 * Класс для передачи стандартного ответа между компонентами
 *
 * Class ResultDTO
 *
 * @package App\Models
 */
class ResultDTO
{
    const OK = 1;

    const FAIL = 0;

    /**
     * @var int
     */
    private $res;

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $data;


    /**
     * ResultDTO constructor.
     *
     * @param int    $res
     * @param string $message
     * @param array  $data
     */
    public function __construct(int $res, string $message, array $data = [], int $code = 200)
    {
        $this->res = $res;
        $this->message = $message;
        $this->data = $data;
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return [
            'code'    => $this->code,
            'res'     => $this->res,
            'message' => $this->message,
            'data'    => $this->data,

        ];
    }


    public function isSuccess(){
        return $this->res === self::OK;
    }
}
