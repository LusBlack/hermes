<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}}'s Followers">
@include(view('profile-followers-only'))
</x-profile>
