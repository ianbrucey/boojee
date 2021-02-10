<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
            {{ __('Book Details') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-8xl mx-auto sm:px-10 lg:px-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                    <div class="p-10 bg-white border-b border-gray-200">

                        <section class="flex flex-col md:flex-row gap-11 py-10 px-5 bg-white rounded-md shadow-lg w-3/4 md:max-w-12xl" style="flex-direction: column;">
                            <div class="text-indigo-500" style="width: 50% !important;">
                                <img src="{{$book->volumeInfo->imageLinks->thumbnail ?? ""}}" width="400">
                            </div>

                            <div class="text-indigo-500">
                                <h3 class="uppercase text-black text-2xl font-medium">{{ $book->volumeInfo->title }} This is a book title</h3>
                                <p>{{ isset($book->volumeInfo->authors) ? "Written by " . implode(", ", $book->volumeInfo->authors) : "" }}</p>
                                <hr>

                                @if(isset($book->volumeInfo->subtitle))
                                    <small class="uppercase">{{ $book->volumeInfo->subtitle }}</small>
                                @endif
                                <br>
                                <small class="text-black">{!!  $book->volumeInfo->description ?? "no description" !!}</small><hr>
                                <small class="text-black">Publisher: {{ $book->volumeInfo->publisher ?? "none" }}</small><br>
                                <small class="text-black">Published Date: {{ $book->volumeInfo->publishedDate ?? "N/A" }}</small><br>
                                <div class="flex gap-0.5 mt-4">
                                    @if(isset($book->volumeInfo->previewLink))
                                        <a id="book-details" href="{{$book->volumeInfo->previewLink}}" class="bg-indigo-600 hover:bg-indigo-500 focus:outline-none transition text-white uppercase px-8 py-3">Preview on Google</a>
                                    @endif
                                    <form id="book-form-{{$book->id}}">
                                        <input name="google_id" type="hidden" value="{{$book->id}}">
                                        <input name="self_link" type="hidden" value="{{$book->selfLink}}">
                                        <input name="title" type="hidden" value="{{$book->volumeInfo->title}}">
                                        <input name="authors" type="hidden" value="{{isset($book->volumeInfo->authors) ? implode(", ", $book->volumeInfo->authors) : ""}}">
                                        <input name="small_thumbnail" type="hidden" value="{{$book->volumeInfo->imageLinks->smallThumbnail ?? ""}}">
                                        <input name="thumbnail" type="hidden" value="{{$book->volumeInfo->imageLinks->thumbnail ?? ""}}">
                                        <input name="language" type="hidden" value="{{$book->volumeInfo->language ?? "n/a"}}">
                                        <input name="description" type="hidden" value="{{$book->volumeInfo->description ?? "no description"}}">
                                        <input name="page_count" type="hidden" value="{{$book->volumeInfo->pageCount ?? ""}}">
                                        <button type="button" id="bookSubmitButton" class="submit-book bg-green-600 hover:bg-indigo-500 focus:outline-none transition text-white uppercase p-3" onclick="submitBook(this)" data-book-form-id="book-form-{{$book->id}}">
                                            + Add to your list
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </section>
                    </div>


            </div>
        </div>
    </div>
    <script>
        function submitBook(obj) {
            ajaxSetup();
            let formId = $(obj).attr("data-book-form-id");
            let form = $("#"+formId);
            $.ajax({
                type: "POST",
                url: "/book",
                data: form.serialize(),
                success: function(data){
                    var res = data;
                    console.table(data);
                    swal("Added successfully!");
                    $(obj).hide();
                },
                error: function (data) {
                    swal(JSON.parse(data.responseText).warning);
                }
            });
        }

    </script>
</x-app-layout>
<?php

