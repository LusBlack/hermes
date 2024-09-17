<x-layout>
    <div class="container py-md-5 container--narrow">
        <form action="/change-username/{{$user->username}}" method="POST">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label for="username" class="text-muted mb-1"><small>Username</small></label>
            <input value="{{old('username', $user->username)}}" name="username" id="username" class="form-control" type="text" placeholder="Pick a username" autocomplete="off" />
            @error('username')
            <p class="m-0 small alert alert-danger shadow-sm">{{$message}}</p>
            @enderror
        </div>
        <button class="btn btn-primary">Save new username</button>
        </form>
    </div>
</x-layout>
