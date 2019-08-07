<?php

namespace App\Controllers;

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

        return view('expressions', compact('expressions'));
    }

    /**
     * Store a new expression in the database.
     */
    public function store()
    {
        $service = new ExpressionService();

        foreach ($service->processFile($_FILES["expressions_xml"]) as $expression) {
            $service->addToDatabase('expressions', $expression);
        }

        return redirect('expressions');
    }

    /**
     * Edit an expression in the database.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function edit($id)
    {
        $expression = App::get('database')->selectOne('expressions', 'id', $id);

        return view('edit', compact('expression'));
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

        return redirect('expressions');
    }

    /**
     * Delete an expression from the database.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function delete($id)
    {
        $expression = App::get('database')->delete('expressions', 'id', $id);

        return redirect('expressions');
    }
}
