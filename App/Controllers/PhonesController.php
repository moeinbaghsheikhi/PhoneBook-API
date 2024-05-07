<?php

namespace App\Controllers;

use App\Traits\ResponseTrait;
use App\Database\QueryBuilder;
use App\Validations\ValidateData;

class PhonesController
{
    use ResponseTrait;
    use ValidateData;

    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    // getAll Phones
    public function index(){
        $phones = $this->queryBuilder->table('phones')->getAll()->execute();

        if(count($phones) < 1) return $this->sendResponse(data: $phones, message: "لیست شماره تلفن خالی است");
        return $this->sendResponse(data: $phones, message: "لیست شماره تلفن ها با موفقیت دریافت شد");
    }

    public function show($id){
        $phone = $this->queryBuilder
            ->table('phones')->get()
            ->where("id", "=", $id)
            ->execute();

        if(!$phone) return $this->sendResponse(data: false, message: "شماره تلفن پیدا نشد", error: true, status: HTTP_BadREQUEST);
        return $this->sendResponse(data: $phone, message: "شماره تلفن پیدا شد");
    }

    public function store($request){
        $this->validate([
            "name||required|min:3|max:15|string",
            "phone_number||required|length:11|string"
        ], $request);

        $newPhone = $this->queryBuilder
            ->table('phones')
            ->insert([
                "name" => $request->name,
                "phone_number" => $request->phone_number
            ])->execute();
        return $this->sendResponse(message: "شماره تلفن جدید با موفقیت ساخته شد");
    }

    public function update($id, $request)
    {
        $isError = false;
        $errorsMessages = [];
        // check set name
        if(!isset($request->name) || empty($request->name)) $isError = true && array_push($errorsMessages, "لطفا نام را وارد کنید");
        if(!isset($request->phone_number) || empty($request->phone_number)) $isError = true && array_push($errorsMessages, "لطفا شماره تلفن را وارد کنید");

        if($isError) return $this->sendResponse(message: $errorsMessages, error: true, status: HTTP_BadREQUEST);

        // check exist phone
        $phone = $this->queryBuilder
            ->table('phones')->get()
            ->where("id", "=", $id)
            ->execute();
        if(!$phone) return $this->sendResponse(data: false, message: "شماره تلفن پیدا نشد", error: true, status: HTTP_BadREQUEST);

        $updatePhone = $this->queryBuilder
            ->table('phones')
            ->update([
                "name" => $request->name,
                "phone_number" => $request->phone_number
            ])->where('id', '=', $id)
            ->execute();

        return $this->sendResponse(message: "شماره تلفن شما با موفقیت آپدیت شد");
    }

    public function destroy($id){
        // check exist phone
        $phone = $this->queryBuilder
            ->table('phones')->get()
            ->where("id", "=", $id)
            ->execute();
        if(!$phone) return $this->sendResponse(data: false, message: "شماره تلفن پیدا نشد", error: true, status: HTTP_BadREQUEST);

        if($id == "*") $deletePhone = $this->queryBuilder->table('phones')->delete()->execute();
        else $deletePhone = $this->queryBuilder->table('phones')->delete()->where('id', '=', $id)->execute();

        return $this->sendResponse(message: "شماره تلفن شما با موفقیت حذف شد");
    }
}