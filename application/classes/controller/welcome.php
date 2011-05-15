<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{	
	    $this->request->response = View::factory('welcome');
	}

	public function action_done()
	{
	    $this->request->response = "Done!";
	}

	public function action_slow_redirect()
	{	    
	    $this->_slow_task();
	}

	public function action_slow_html()
	{	    
	    $this->_slow_task();
	}

	public function action_slow_file()
	{	    
	    $this->_slow_task();
	}

	protected function _slow_task()
	{
	    try
	    {
		$task = SlowTask::begin($this->request, "Beginning a slow task")
			    ->progress_range(0, 30);

		for ($i=0; $i<30; $i++)
		{
		   sleep(1);
		   $task->progress("i = $i");
		}
		
		switch ($this->request->action)
		{
		    case 'slow_file':
			$task->complete(SlowTask_Complete_SendFile::factory($task, __FILE__));
			break;
		    break;
		    case 'slow_redirect':
			$task->complete(
			    new SlowTask_Complete_Redirect(
				    Route::url('default', array('action'=>'done'))));
		    break;
		    case 'slow_html':
			$task->complete(
			    new SlowTask_Complete_HTML(
				    "<h1>Slowtask was done!</h1><p>"
				    .HTML::anchor('/',"Go back")."</p>"));

		    break;
		}
		return;
	    }
	    catch (SlowTask_Abort_Exception $e)
	    {
		$task->complete(new SlowTask_Complete_HTML("You aborted the process!"));
	    }
	    catch (Exception $e)
	    {
		$task->complete(new SlowTask_Complete_HTML(Kohana::exception_text($e)));
		throw $e;
	    }

	}

} // End Welcome
