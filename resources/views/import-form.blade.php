@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Import Form') }}</div>

                    <div class="card-body">
                        <form action="{{ route('import') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input class="form-control mb-3" type="file" name="file" accept=".xlsx, .xls">
                            @error('file') {{$message}}  @enderror
                            <label for="class">Select Import Class:</label>
                            <select name="class"  class="form-select mb-3">
                                @foreach($importClasses as $importClass)
                                    <option value="{{ $importClass }}">{{ $importClass }}</option>
                                @endforeach
                            </select>
                            @error('class') {{$message}}  @enderror
                            <button class="btn btn-primary" type="submit">Import Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


