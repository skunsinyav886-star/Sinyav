<!-- <h1>User List</h1>
<a href="./?page=user/create">Create New</a> -->
<div class="container">
    <div class="d-flex justify-content-between">
        <h3>User List</h3>
        <a href="./page=user/create" class="btn btn-success">Create New</a>
    </div>

    <table class="table table-striped">
        <thead></thead>
        <tr>
            <th>#</th>
            <th>photo</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            <?php
            $users = getUsers();
            $count = 1;
            while ($row = $users->fetch_object()){
            ?>
            <tr>
                <td><?php echo $count ?></td>
                <td><img src=" <?php echo $row->photo ?? './assets/image/emptyuser.png' ?> "></td>
                <td>John</td>
                <td>
                    <botton class="btn btn-primary">Update</botton>
                    <botton class="btn btn-danger">Delete</botton>
                </td>
            </tr>
            <?php
            $count++;
            }
            ?>
        </tbody>
    </table>
</div>