<?php

use DefStudio\Burnout\Models\BurnoutEntry;

/** @var BurnoutEntry[] $entries */
?>

@extends('burnout::app')


@section('content')


    <table class="table-auto">
        <thead>
        <tr>
            <th class="px-4 py-2">Date</th>
            <th class="px-4 py-2">Error</th>
            <th class="px-4 py-2"></th>
            <th class="px-4 py-2">
                <form method="post" id="destroy-all" action="{{route('burnout.clear')}}">
                    @method('delete')
                    @csrf
                </form>
                <button form="destroy-all">Delete All</button>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($entries as $entry)
            <tr>
                <td class="border px-4 py-2">{!! $entry->created_at->format('d/m/Y&\nb\s\p;H:i:s') !!}</td>
                <td class="border px-4 py-2">{{$entry->message}}</td>
                <td class="border px-4 py-2"><a href="{{route('burnout.show', $entry)}}">View</a></td>
                <td class="border px-4 py-2">
                    <form method="post" id="destroy-{{$entry->id}}" action="{{route('burnout.destroy', $entry)}}">
                        @method('delete')
                        @csrf
                    </form>
                    <button form="destroy-{{$entry->id}}">Delete</button>
                </td>
            </tr>
        @endforeach


        </tbody>
    </table>





@endsection
