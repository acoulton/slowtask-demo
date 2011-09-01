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
	    /*
	     * Always wrap your whole task in a try/catch for two reasons:
	     *  - this request is now disconnected from the browser, and so you
	     *    will need to use the SlowTask API to report errors back to the
	     *    user - your usual error handler code will not work.
	     *  - if the user aborts, a SlowTask_Abort_Exception will be thrown
	     *    and you need to catch this and clean up accordingly.
	     */
	    try
	    {
		/*
		 * Begin the SlowTask - this sends the progress bar to the browser
		 * and closes the connection. From here on in, you're running headless
		 */
		$task = SlowTask::begin($this->request, "Beginning a slow task")
			    /*
			     * If you have a fixed range and a linear task, let
			     * SlowTask calculate your % complete for you.
			     */
			    ->progress_range(0, 30);

		for ($i=0; $i<30; $i++)
		{
		   // Do whatever you need to
		   sleep(1);
		   /*
		    * And at regular intervals update the progress bar, optionally
		    * updating the text description as well as the progress. Note you
		    * can set a step of 0 to just update the text, or a greater step
		    * if progress has moved on or you are calculating percentages
		    * manually.
		    *
		    * SlowTask::progress() also calls SlowTask::_yield() to test
		    * for user abort and will throw an exception in this case.
		    */
		   $task->progress("i = $i");
		}

		// In a real app, you'd generally have a single completion type per task
		switch ($this->request->action)
		{
		    case 'slow_file':
			/*
			 * This will download a file - note you can also set an HTML
			 * message through completion_message() which will show while
			 * the file downloads.
			 */
			$task->complete(SlowTask_Complete_SendFile::factory($task, __FILE__));
			break;
		    break;
		    case 'slow_redirect':
			// A straightforward redirect (window.location) to the given URI
			$task->complete(
			    new SlowTask_Complete_Redirect(
				    Route::url('default', array('action'=>'done'))));
		    break;
		    case 'slow_html':
			/*
			 * You can pass in any HTML here, note that it's a fragment
			 * that will replace the progress div, not a fully laid out
			 * page.
			 */
			$task->complete(
			    new SlowTask_Complete_HTML(
				    "<h1>Slowtask was done!</h1><p>"
				    .HTML::anchor('/',"Go back")."</p>"));

		    break;
		}
		return;
	    }
	    
	    /*
	     * You must always catch and handle exceptions, at least to complete
	     * the SlowTask. The client script will eventually detect that the
	     * heartbeat has stopped, but this takes longer and will give a very
	     * generic error message to the user.
	     */
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
