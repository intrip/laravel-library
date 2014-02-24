<?php namespace Jacopo\Library\Form;
/**
 * Class FormModel
 *
 * Class to save form data associated to a model
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Library\Validators\ValidatorInterface;
use Jacopo\Library\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\MessageBag;
use Jacopo\Library\Exceptions\NotFoundException;
use Jacopo\Authentication\Exceptions\PermissionException;
use Event;

class FormModel implements FormInterface{

    /**
     * Validator
     * @var \Jacopo\Library\Validators\ValidatorInterface
     */
    protected $v;
    /**
     * Repository used to handle data
     * @var
     */
    protected $r;
    /**
     * Name of the model id field
     * @var string
     */
    protected $id_name = "id";
    /**
     * Validaton errors
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    public function __construct(ValidatorInterface $validator, $repository)
    {
        $this->v = $validator;
        $this->r = $repository;
    }

    /**
     * Process the input and calls the repository
     * @param array $input
     * @throws \Jacopo\Library\Exceptions\JacopoExceptionsInterface
     */
    public function process(array $input)
    {
        if($this->v->validate($input))
        {
            Event::fire("form.processing", array($input));
            return $this->callRepository($input);
        }
        else
        {
            $this->errors = $this->v->getErrors();
            throw new ValidationException;
        }
    }

    /**
     * Calls create or update depending on giving or not the id
     * @param $input
     * @throws \Jacopo\Library\Exceptions\NotFundException
     */
    protected function callRepository($input)
    {
        if($this->isUpdate($input))
        {
            try
            {
                $obj = $this->r->update($input[$this->id_name], $input);
            }
            catch(ModelNotFoundException $e)
            {
                $this->errors = new MessageBag(array("model" => "Elemento non trovato"));
                throw new NotFoundException();
            }
            catch(PermissionException $e)
            {
                $this->errors = new MessageBag(array("model" => "Non è possibile modificare questo elemento"));
                throw new PermissionException();
            }
        }
        else
        {
            try
            {
                $obj = $this->r->create($input);
            }
            catch(NotFoundException $e)
            {
                $this->errors = new MessageBag(array("model" => $e->getMessage()));
                throw new NotFoundException();
            }
        }

        return $obj;
    }

    /**
     * Check if the operation is update or create
     * @param $input
     * @return booelan $update update=true create=false
     */
    protected function isUpdate($input)
    {
        return (isset($input[$this->id_name]) && ! empty($input[$this->id_name]) );
    }

    /**
     * Run delete on the repository
     * @param $input
     * @throws \Jacopo\Library\Exceptions\NotFoundException
     * @todo test with exceptions
     */
    public function delete(array $input)
    {
        if(isset($input[$this->id_name]) && ! empty($input[$this->id_name]))
        {
            try
            {
                $this->r->delete($input[$this->id_name]);
            }
            catch(ModelNotFoundException $e)
            {
                $this->errors = new MessageBag(array("model" => "Elemento non esistente"));
                throw new NotFoundException();
            }
            catch(PermissionException $e)
            {
                $this->errors = new MessageBag(array("model" => "Non è possibile cancellare questo elemento, verifica i tuoi permessi e che l'elemento non sia associato ad altri."));
                throw new PermissionException();
            }
        }
        else
        {
            $this->errors = new MessageBag(array("model" => "Id non fornito"));
            throw new NotFoundException();
        }
    }
    
    public function getErrors()
    {
        return $this->errors;
    }

} 