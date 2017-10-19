<?php

/*
    Maintanence controller
*/
class Mtce extends Application {

    private $items_per_page = 10;

    public function index()
    {
        $this->page(1);
    }

    // Show a single page of todo items
    public function show_page ($tasks) {
        $role = $this->session->userdata('userrole');
        $this->data['pagetitle'] = 'TODO List Maintenance ('. $role . ')';

        // build the task presentation output
        $result = '';   // start with an empty array        
        foreach ($tasks as $task) { 
            if (!empty($task->status))
                $task->status = $this->app->status($task->status);
            $result .= $this->parser->parse('oneitem',(array)$task,true);
        }

        // and then pass them on
        $this->data['display_tasks'] = $result;
        $this->data['pagebody'] = 'itemlist';
        $this->render();
    }

    // Extract & handle a page of items, defaulting to the beginning
    public function page  ($num = 1) {
        $records = $this->tasks->all (); // get all the tasks
        $tasks   = array ();

        // use a foreach loop, because the record indicies may not be sequential
        $index = 0;
        $count = 0;
        $start = ($num - 1) * $this->items_per_page;
        foreach ($records as $task) {
            if ($index++ >= $start) {
                $tasks[] = $task;
                ++$count;
            }
            if ($count >= $this->items_per_page)
                break;
        }
        $this->data['pagination'] = $this->pagenav($num);
        $this->show_page($tasks);
    }

    // Build the pagination navbar
    public function pagenav ($num) {
        $lastpage = ceil ($this -> tasks -> size () / $this -> items_per_page);
        $params = array (
            'first'    => 1,
            'previous' => (max ($num - 1, 1)),
            'next'     => (min ($num + 1, $lastpage)),
            'last'     => $lastpage 
        );

        return $this->parser->parse ('itemnav', $params, true);
    }

}

?>