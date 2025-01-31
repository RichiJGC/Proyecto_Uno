@extends('layouts.app')

@section('title', 'Phone Form')

@section('content')
<div class="block mx-auto my-12 p-8 bg-white w-1/3 border border-gray-200 rounded-lg shadow-lg">
    <h1 class="text-3xl text-center font-bold text-black">Telefono</h1>

    <form class="mt-4" method="POST" action="{{ route('auth.store') }}">
        @csrf

        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="password" value="{{ $password }}">
    
        <input type="tel" class="border border-gray-200 rounded-md bg-gray-200 w-full
        text-lg placeholder-gray-900 p-2 my-2 focus:bg-white" placeholder="Phone Number"
        id="phone_number" name="phone_number">

        @error('phone_number')
        <p class="border border-black-500 rounded-md bg-red-100 w-full
          text-black-600 p-2 my-2">* {{ $message }}</p>
        @enderror

        <button type="submit" class="rounded-md bg-indigo-500 w-full text-lg
        text-white font-semibold p-2 my-3 hover:bg-indigo-600">Send</button>
    </form>
</div>
@endsection
