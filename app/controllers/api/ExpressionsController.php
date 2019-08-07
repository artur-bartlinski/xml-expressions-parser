<?php

namespace App\Controllers\Api;

use App\Core\App;
use app\services\ExpressionService;

class ExpressionsController
{
    /**
     * Show all expressions.
     */
    public function index()
    {
        $expressions = App::get('database')->selectAll('expressions');
        header('Content-Type: application/json');

        echo json_encode($expressions);
    }

    /**
     * Show a single expression.
     * @param $id
     */
    public function view($id)
    {
        $expression = App::get('database')->selectOne('expressions', 'id', $id);
        header('Content-Type: application/json');

        echo json_encode($expression);
    }

    /**
     * Store a new expression in the database.
     */
    public function store()
    {
        $data = $_POST['expression']; //TODO: validation

        App::get('database')->insert('expressions', $data);

        return redirect('api/expressions');
    }

    /**
     * Process XML file and store expressions to the database.
     */
    public function storeXML()
    {
        $service = new ExpressionService();

        foreach ($service->processFile($_FILES["expressions_xml"]) as $expression) {
            $service->addToDatabase('expressions', $expression);
        }

        return redirect('api/expressions');
    }

    /**
     * Update an expression in the database.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     * @throws \Exception
     */
    public function update()
    {
        $expression = $_POST['expression'];
        $expressionId = $_POST['id'];

        if (is_int($expressionId) && preg_match('/^[0-9\-\(\)+*/]+$/', $expression))
            App::get('database')->update('expressions', 'id', $expressionId, ['expression' => $expression]);
        else
            throw new \Exception('Expression did not pass validation and cannot be updated!');

        return redirect('api/expressions');
    }

    /**
     * Delete an expression from the database.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function delete($id)
    {
        $expression = App::get('database')->delete('expressions', 'id', $id);

        return redirect('api/expressions');
    }
}
