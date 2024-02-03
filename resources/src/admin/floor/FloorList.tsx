import React, { useEffect, useState } from "react";
import Breadcrumb from "@src/layouts/full/shared/breadcrumb/Breadcrumb2";
import PageContainer from "@src/components/container/PageContainer";
import CommonTableList from "@src/common/CommonTableList";
import BlankCard from "@src/components/shared/BlankCard";
import axios from "axios";
import { useApiMessages } from '@src/common/Utils'; // Import the utility

const BCrumb = [
    {
        to: "/super-admin/dashboard",
        title: "Society Management",
    },
    {
        title: "Floor List",
    },
];

const FloorList = () => {
    const { showSuccessMessage, showErrorMessage, renderSuccessMessage, renderErrorMessage } = useApiMessages();
    const [page, setPage] = useState("");
    const [totalCount, setTotalCount] = useState("");
    const [keyword, setkeyword] = useState("");
    const [loading, setLoading] = useState(true);
    const [pageSortOrder, setpageSortOrder] = useState('');
    //Site Tokens
    const token = localStorage.getItem("authToken");
    const society_token = localStorage.getItem("societyToken");

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
            id: "tower_name",
            numeric: false,
            disablePadding: false,
            label: "Tower",
            enableSorting: true,
        },
        {
            id: "floor_name",
            numeric: false,
            disablePadding: false,
            label: "Floor",
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
        "tower_name",
        "floor_name",
    ];

    const rowSettings = {
    };

    //show = 1 for show the button show = 0 then button not show
    const actionSettings = {
        actions: {
            edit: { url: "/admin/edit-floor/", show: "1" },
            delete: { url: "", show: "1" },
        },
    };
    const addUrl = "/admin/add-floor";
    const [datas, setData] = useState([]); // State to store the fetched data
    const [totalPages, settotalPages] = useState([]); // State to store the fetched data
    useEffect(() => {
        //when the component mounts
        fetchData();
        const storedSuccessMessage = sessionStorage.getItem("successMessage");
        if (storedSuccessMessage) {
            showSuccessMessage(storedSuccessMessage);
            sessionStorage.removeItem("successMessage");
        }
    }, [setData]);

    // Function to fetch data from the API
    const fetchData = async (perPage = "", page = "", sortBy = "",search="",IsAsc="") => {
        var sortData = (sortBy == "") ? "id" : sortBy;
        var IsSort = (IsAsc == "") ? "asc" : IsAsc;
        setpageSortOrder(IsSort);
        if (page == "") {
            setPage("1");
            page='1';
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
            const API_URL = appUrl + "/api/list-floor";
            const response = await axios.post(API_URL, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "society_id": `${society_token}`,
                },
            });
            if (response && response.data && response.data.data) {
                setData(response.data.data.data);
                settotalPages(response.data.data.last_page);
                setTotalCount(response.data.data.total);
            } else {
                setData([]);
                settotalPages(1);
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
            const API_URL = appUrl + "/api/delete-floor";
            const response = await axios.post(API_URL, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "society_id": `${society_token}`,
                },
            });
            showSuccessMessage(response.data.message);
            fetchData();
            //console.log("Success deleting data:", response.data);
        } catch (error) {
            showErrorMessage(error);
        }
    };
    //For Download Excel variable
    const excelName = "floor_list";
    const excelApiUrl = "";
    return (
        <PageContainer
            title="Floor List"
            description="this is Floor List page"
        >
            <Breadcrumb title="" />
            {renderSuccessMessage()}
            {renderErrorMessage()}
            <BlankCard>
                {/* ------------------------------------------- */}
                {/* Left part */}
                {/* ------------------------------------------- */}
                <CommonTableList
                    pageTitle={"Floor List"}
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

export default FloorList;
