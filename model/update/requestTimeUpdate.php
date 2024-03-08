<?php
if(isset($_POST['schedId']) && isset($_POST['time'])){
    $schedId = filter_input(INPUT_POST, 'schedId', FILTER_SANITIZE_NUMBER_INT);
    $time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);

    if($schedId && $time){
        $updateQuery = "UPDATE tbl_event_sched
                        SET timein  = :new_time
                        WHERE schedule_id = :schedule_id";
        $smthUpdate = $conn->prepare($updateQuery);

        $smthUpdate->bindParam(':new_time', $time);
        $smthUpdate->bindParam(':schedule_id', $schedId);

        if($smthUpdate->execute()){
            // Delete the request after successful update
            $deleteReq = "DELETE FROM tbl_request_time WHERE schedule_id = :schedule_id";
            $smthDelete = $conn->prepare($deleteReq);
            $smthDelete->bindParam(':schedule_id', $schedId);
            
            if($smthDelete->execute()){
                echo "<script>
                    alert('Data updated successfully');
                    window.location.href = '';
                </script>";
            }
        }else{
            echo "<script>
                alert('update failed');
            </script>";
        }
    }else{
        echo "<script>
            alert('Invalid input');
        </script>";
    }
}

if(isset($_POST['rejectId'])){
    if(isset($_POST['reqId'])){
        $reqId = filter_input(INPUT_POST, 'reqId', FILTER_SANITIZE_NUMBER_INT);

        if($reqId === false){
            echo "<script>
                alert('reqId failed filter_input');
            </script>";
        }elseif($reqId === null){
            echo "<script>
                alert('reqId is not set');
            </script>";
        }elseif($reqId){
            $deleteReq = "DELETE FROM tbl_request_time WHERE request_id = :req_id";
            $smthDelete = $conn->prepare($deleteReq);
            $smthDelete->bindParam(':req_id', $reqId);

            if($smthDelete->execute()){
                echo "<script>
                    alert('Request deleted successfully');
                    window.location.href = '';
                </script>";
            }else{
                echo "<script>
                    alert('delete failed');
                </script>";
            }
        }else{
            echo "<script>
                alert('Invalid reqId');
            </script>";
        }
    }else{
        echo "<script>
            alert('reqId is not set in POST');
        </script>";
    }
}