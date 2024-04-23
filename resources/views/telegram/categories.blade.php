<b>{{$type}}</b>
@foreach($categories as $category)
  {{$category->name}}
@foreach($category->children as $child)
    - {{$child->name}}
@endforeach
@endforeach
