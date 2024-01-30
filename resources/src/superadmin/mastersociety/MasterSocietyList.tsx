import React, { useEffect, useState } from "react";
import Breadcrumb from "@src/layouts/full/shared/breadcrumb/Breadcrumb2";
import PageContainer from "@src/components/container/PageContainer";
import CommonTableList from "@src/common/CommonTableList";
import BlankCard from "@src/components/shared/BlankCard";
import axios from "axios";
import { Alert, AlertTitle } from "@mui/material";
import Snackbar from "@mui/material/Snackbar";
import { Portal } from '@mui/base';

const BCrumb = [
    {
        to: "/super-admin/dashboard",
        title: "Home",
    },
    {
        title: "Society Management",
    },
];

const MasterSocietyList = () => {
    const [isSuccessVisible, setIsSuccessVisible] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const [isErrorVisible, setIsErrorVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");
    //const [perPage, setPerPage] = useState('');
    const [page, setPage] = useState("");
    const [totalCount, setTotalCount] = useState("");
    const [keyword, setkeyword] = useState("");
    const [loading, setLoading] = useState(true);
    const [pageSortOrder, setpageSortOrder] = useState('');
    //let page = '';
    let perPage = "";
    const headCells = [
        {
            id: 'id',
            numeric: false,
            disablePadding: false,
            label: 'Sr. No.',
            enableSorting:true,
        },
        {
            id: "society_unique_code",
            numeric: false,
            disablePadding: false,
            label: "Society Code",
            enableSorting: true,
        },
        {
            id: "society_name",
            numeric: false,
            disablePadding: false,
            label: "Society Name",
            enableSorting: true,
        },
        {
            id: "address",
            numeric: false,
            disablePadding: false,
            label: "Address",
            enableSorting: true,
        },
        {
            id: "email",
            numeric: false,
            disablePadding: false,
            label: "Email",
            enableSorting: true,
        },
        {
            id: "phone_number",
            numeric: false,
            disablePadding: false,
            label: "Phone Number",
            enableSorting: true,
        },
        {
            id: "action",
            numeric: false,
            disablePadding: false,
            label: "Action",
            enableSorting: false,
        },
    ];

    const dataColumns = [
        'srno',
        "society_unique_code",
        "society_name",
        "address",
        "email",
        "phone_number",
    ];

    const rowSettings = {
    };

    //show = 1 for show the button show = 0 then button not show
    const actionSettings = {
        actions: {
            edit: { url: "/super-admin/edit-society/", show: "1" },
            delete: { url: "", show: "0" },
        },
    };
    const addUrl = "/super-admin/add-society";
    const [datas, setData] = useState([]); // State to store the fetched data
    const [totalPages, settotalPages] = useState([]); // State to store the fetched data
    useEffect(() => {
        //when the component mounts
        fetchData();
        const storedSuccessMessage = sessionStorage.getItem("successMessage");

        if (storedSuccessMessage) {
            setIsSuccessVisible(true);
            setSuccessMessage(storedSuccessMessage);
            sessionStorage.removeItem("successMessage");
        }
    }, [setData]);

    // Function to fetch data from the API
    const fetchData = async (perPage = "", page = "", sortBy = "",search="",IsAsc="") => {
        var sortData = (sortBy == "") ? "id" : sortBy;
        var IsSort = (IsAsc == "") ? "desc" : IsAsc;
        setpageSortOrder(IsSort);
        if (page == "") {
            setPage("1");
        }
        
        if (perPage == "") {
            const setPerPage = localStorage.getItem('rowsPerPage');
            const currentURL = localStorage.getItem('currentURL');
            perPage = (setPerPage) ? setPerPage : '10';
            sessionStorage.removeItem("searchKeyword");
        }
        try {
            const formData = new FormData();
            formData.append("page", page);
            formData.append("sortBy", sortData);
            formData.append('sortOrder', IsSort);
            formData.append("perPage", perPage);
            const searchKeyword = sessionStorage.getItem("searchKeyword");
            formData.append(
                "keyword",
                searchKeyword !== null ? searchKeyword : ""
            );
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = appUrl + "/api/list-society";
            const token = localStorage.getItem("authToken");
            const response = await axios.post(API_URL, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            if (response && response.data && response.data.data) {
                setData(response.data.data.data);
                settotalPages(response.data.data.last_page);
                setTotalCount(response.data.data.total);
            } else {
                setData(response.data);
                settotalPages(response.data.data.last_page);
                setTotalCount(0);
                console.error("Error: Unexpected response structure", response);
            }
        } catch (error) {
            console.error("Error fetching data:", error); // Log any errors
        } finally {
            setLoading(false);
        }
    };
    const handleSearch = (keyword1, rowsPerPage) => {
        fetchData(rowsPerPage);
    };

    //Handle the delete function
    const handleDelete = async (id) => {
        try {
            const formData = new FormData();
            formData.append("id", id);
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = appUrl + "/api/delete-society";
            const token = localStorage.getItem("authToken");
            const response = await axios.post(API_URL, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            setIsSuccessVisible(true);
            setSuccessMessage(response.data.message);
            fetchData();
            //console.log("Success deleting data:", response.data);
        } catch (error) {
            setIsErrorVisible(true);
            if (
                error.response &&
                error.response.data &&
                error.response.data.message
            ) {
                setErrorMessage(error.response.data.message);
            } else {
                setErrorMessage("An error occurred while deleting the terms.");
            }
        }
    };
    //For Download Excel variable
    const excelName = "society_list";
    const excelApiUrl = "";
    return (
        <PageContainer
            title="Society Management"
            description="Society Management"
        >
            <Breadcrumb title="" />
            <Portal>
            <Snackbar
                anchorOrigin={{ vertical: "top", horizontal: "right" }}
                open={isErrorVisible}
                autoHideDuration={3000}
                onClose={() => setIsErrorVisible(false)}
            >
                <Alert severity="error">
                    <div style={{ fontSize: "14px", padding: "2px" }}>
                        {errorMessage && <div>{errorMessage}</div>}
                    </div>
                </Alert>
            </Snackbar>
            </Portal>
            <Portal>
            <Snackbar
                anchorOrigin={{ vertical: "top", horizontal: "right" }}
                open={isSuccessVisible}
                autoHideDuration={3000}
                onClose={() => setIsSuccessVisible(false)}
            >
                <Alert severity="success">
                    <div style={{ fontSize: "14px", padding: "2px" }}>
                        {successMessage && <div>{successMessage}</div>}
                    </div>
                </Alert>
            </Snackbar>
            </Portal>
            <BlankCard>
                {/* ------------------------------------------- */}
                {/* Left part */}
                {/* ------------------------------------------- */}
                <CommonTableList
                    pageTitle={"Society Management"}
                    headCells={headCells}
                    dataRow={datas}
                    totalPage={totalPages}
                    page={page}
                    dataColumns={dataColumns}
                    handleSearch={handleSearch}
                    handleDelete={handleDelete}
                    rowSettings={rowSettings}
                    actionSettings={actionSettings}
                    fetchData={fetchData}
                    totalCount={totalCount}
                    addUrl={addUrl}
                    excelName={excelName}
                    excelApiUrl={excelApiUrl}
                    loading={loading} pageSortOrder={pageSortOrder}
                />
            </BlankCard>
        </PageContainer>
    );
};

export default MasterSocietyList;
